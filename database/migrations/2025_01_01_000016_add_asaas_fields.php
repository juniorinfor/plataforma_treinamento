<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // companies: id do cliente Asaas
        Schema::table('companies', function (Blueprint $table) {
            $table->string('asaas_customer_id')->nullable()->after('document');
        });

        // subscriptions: ciclo + próximo vencimento
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('cycle', 10)->default('MONTHLY')->after('payment_gateway'); // MONTHLY | YEARLY
            $table->date('next_due_date')->nullable()->after('cycle');
        });

        // invoices: link de pagamento + tipo
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('billing_type', 20)->nullable()->after('currency');   // PIX | BOLETO | CREDIT_CARD
            $table->string('payment_url')->nullable()->after('gateway_invoice_id');
            $table->string('pix_qr_code', 2048)->nullable()->after('payment_url');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('asaas_customer_id');
        });
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['cycle', 'next_due_date']);
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['billing_type', 'payment_url', 'pix_qr_code']);
        });
    }
};
