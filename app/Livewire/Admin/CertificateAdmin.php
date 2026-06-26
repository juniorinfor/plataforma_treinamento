<?php

namespace App\Livewire\Admin;

use App\Models\CertificateTemplate;
use App\Models\Company;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Certificados')]
class CertificateAdmin extends Component
{
    public ?int $selectedCompany = null;

    public bool   $showModal      = false;
    public ?int   $editingId      = null;
    public string $name           = '';
    public string $html_template  = '';
    public string $css            = '';
    public bool   $is_default     = false;

    public function mount(): void
    {
        if (!auth()->user()->isPlatformAdmin()) {
            $this->selectedCompany = auth()->user()->company_id;
        }
    }

    protected function cid(): ?int { return $this->selectedCompany; }

    #[Computed]
    public function companies()
    {
        return auth()->user()->isPlatformAdmin()
            ? Company::orderBy('name')->get(['id', 'name'])
            : collect();
    }

    #[Computed]
    public function templates()
    {
        $q = CertificateTemplate::query();
        if ($this->cid()) {
            $q->where('company_id', $this->cid());
        }
        return $q->orderByDesc('is_default')->orderBy('name')->get();
    }

    public function openCreate(): void
    {
        $this->reset('editingId', 'name', 'html_template', 'css');
        $this->is_default = false;
        $this->html_template = $this->defaultTemplate();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $tpl = CertificateTemplate::findOrFail($id);
        $this->editingId      = $id;
        $this->name           = $tpl->name;
        $this->html_template  = $tpl->html_template ?? '';
        $this->css            = $tpl->css ?? '';
        $this->is_default     = $tpl->is_default;
        $this->showModal      = true;
    }

    public function save(): void
    {
        $this->validate([
            'name'          => 'required|string|max:100',
            'html_template' => 'required|string',
            'css'           => 'nullable|string',
        ]);

        $cid = $this->cid() ?? auth()->user()->company_id;

        // Only one default per company
        if ($this->is_default) {
            CertificateTemplate::where('company_id', $cid)
                ->where('id', '!=', $this->editingId ?? 0)
                ->update(['is_default' => false]);
        }

        CertificateTemplate::updateOrCreate(['id' => $this->editingId], [
            'company_id'    => $cid,
            'name'          => $this->name,
            'html_template' => $this->html_template,
            'css'           => $this->css ?: null,
            'is_default'    => $this->is_default,
        ]);

        unset($this->templates);
        $this->showModal = false;
    }

    public function setDefault(int $id): void
    {
        $tpl = CertificateTemplate::findOrFail($id);
        CertificateTemplate::where('company_id', $tpl->company_id)->update(['is_default' => false]);
        $tpl->update(['is_default' => true]);
        unset($this->templates);
    }

    public function delete(int $id): void
    {
        CertificateTemplate::destroy($id);
        unset($this->templates);
    }

    private function defaultTemplate(): string
    {
        return '<div class="certificate"><h1>{{student_name}}</h1><p>concluiu com êxito o curso</p><h2>{{course_name}}</h2><p>em {{completion_date}}</p></div>';
    }

    public function render()
    {
        return view('livewire.admin.certificate-admin');
    }
}
