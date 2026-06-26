<?php

namespace App\Livewire\Forum;

use App\Models\ForumCategory;
use App\Models\ForumPost;
use App\Models\ForumThread;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Fórum')]
class ForumIndex extends Component
{
    public ?int  $activeCategoryId = null;
    public ?int  $openedThreadId   = null;
    public bool  $showNewModal     = false;

    public string $newTitle      = '';
    public string $newContent    = '';
    public ?int   $newCategoryId = null;

    public string $replyContent = '';

    // ── Helpers ───────────────────────────────────────────────────────

    protected function cid(): ?int
    {
        return auth()->user()->company_id;
    }

    // ── Computed ──────────────────────────────────────────────────────

    #[Computed]
    public function categories()
    {
        if (!$this->cid()) return collect();
        return ForumCategory::where('company_id', $this->cid())
            ->where('is_active', true)
            ->withCount('threads')
            ->orderBy('sort_order')
            ->get();
    }

    #[Computed]
    public function threads()
    {
        if (!$this->cid()) return collect();

        $q = ForumThread::where('company_id', $this->cid())
            ->with(['user:id,name', 'category:id,name,color'])
            ->withCount('posts');

        if ($this->activeCategoryId) {
            $q->where('forum_category_id', $this->activeCategoryId);
        }

        return $q->orderByDesc('is_pinned')
                 ->orderByDesc('last_post_at')
                 ->orderByDesc('created_at')
                 ->get();
    }

    #[Computed]
    public function openedThread(): ?ForumThread
    {
        if (!$this->openedThreadId) return null;
        return ForumThread::with([
            'user:id,name',
            'category:id,name,color',
            'posts' => fn ($q) => $q->with('user:id,name')->orderBy('created_at'),
        ])->find($this->openedThreadId);
    }

    #[Computed]
    public function topContributors()
    {
        if (!$this->cid()) return collect();
        return DB::table('forum_posts')
            ->join('forum_threads', 'forum_posts.forum_thread_id', '=', 'forum_threads.id')
            ->join('users', 'forum_posts.user_id', '=', 'users.id')
            ->where('forum_threads.company_id', $this->cid())
            ->select('users.id', 'users.name', DB::raw('COUNT(forum_posts.id) as posts_count'))
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('posts_count')
            ->limit(5)
            ->get();
    }

    // ── Actions ───────────────────────────────────────────────────────

    public function setCategory(?int $id): void
    {
        $this->activeCategoryId = $id;
        unset($this->threads);
    }

    public function openThread(int $threadId): void
    {
        $this->openedThreadId = $threadId;
        $this->replyContent   = '';
        ForumThread::where('id', $threadId)->increment('views_count');
        unset($this->openedThread);
    }

    public function closeThread(): void
    {
        $this->openedThreadId = null;
        unset($this->openedThread, $this->threads);
    }

    public function createThread(): void
    {
        $this->validate([
            'newTitle'      => 'required|string|min:5|max:255',
            'newContent'    => 'required|string|min:10',
            'newCategoryId' => 'required|integer|exists:forum_categories,id',
        ], [
            'newTitle.required'      => 'Título obrigatório.',
            'newTitle.min'           => 'Título muito curto (mínimo 5 caracteres).',
            'newContent.required'    => 'Conteúdo obrigatório.',
            'newContent.min'         => 'Escreva pelo menos 10 caracteres.',
            'newCategoryId.required' => 'Selecione uma categoria.',
            'newCategoryId.exists'   => 'Categoria inválida.',
        ]);

        $user = auth()->user();
        abort_unless($user->company_id, 403);

        $thread = ForumThread::create([
            'company_id'        => $user->company_id,
            'forum_category_id' => $this->newCategoryId,
            'user_id'           => $user->id,
            'title'             => $this->newTitle,
            'content'           => $this->newContent,
            'last_post_at'      => now(),
            'last_post_user_id' => $user->id,
        ]);

        $this->reset(['newTitle', 'newContent', 'newCategoryId', 'showNewModal']);
        unset($this->threads);
        $this->openThread($thread->id);
    }

    public function submitReply(): void
    {
        $this->validate(['replyContent' => 'required|string|min:2'], [
            'replyContent.required' => 'Escreva sua resposta.',
            'replyContent.min'      => 'Resposta muito curta.',
        ]);

        $user   = auth()->user();
        $thread = ForumThread::where('company_id', $user->company_id)
                             ->findOrFail($this->openedThreadId);

        abort_if($thread->is_locked, 403, 'Thread bloqueado.');

        ForumPost::create([
            'forum_thread_id' => $thread->id,
            'user_id'         => $user->id,
            'content'         => $this->replyContent,
        ]);

        $thread->increment('posts_count');
        $thread->update(['last_post_at' => now(), 'last_post_user_id' => $user->id]);

        $this->replyContent = '';
        unset($this->openedThread, $this->threads);
    }

    public function render()
    {
        return view('livewire.forum.forum-index');
    }
}
