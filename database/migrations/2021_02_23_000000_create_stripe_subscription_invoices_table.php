<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStripeSubscriptionInvoicesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('stripe_subscription_invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('subscription_invoice_id')->unique();
            $table->string('status');
            $table->string('stripe_invoice_id');
            $table->string('currency_code');
            $table->decimal('total', 22, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('stripe_subscription_invoices');
    }

}
