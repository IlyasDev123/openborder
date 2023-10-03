<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSubscriptionsDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_subscriptions_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('plan_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->tinyInteger('status')->default('1')->comment('1: active, 0: inactive');
            $table->float('total_amount')->default(0)->comment('Amount');
            $table->integer('quantity')->default(0)->comment('Amount');
            $table->tinyInteger('subscription_type')->default(null)->comment('1: monthly, 2: yearly, 3:day')->nullable();

            //Stripe
            $table->string('stripe_customer', 150)->nullable()->comment('Stripe Customer');

            $table->string('stripe_subscription_id', 150)->nullable();
            $table->string('stripe_latest_invoice', 150)->nullable()->comment('Stripe Invoice Code for this Subscription Period');

            $table->dateTime('package_start_date')->nullable()->comment('Current Invoiced Period Start');
            $table->dateTime('package_end_date')->nullable()->comment('Current Invoiced Period END');
            $table->dateTime('stripe_billing_start_date')->nullable()->comment('Billing Start Data for this Subscription');


            $table->dateTime('stripe_start_at')->nullable()->comment('Subscription Original Start Date');
            $table->dateTime('stripe_last_change_at')->nullable()->comment('Subscription Last Change Date');
            $table->dateTime('stripe_canceled_at')->nullable()->comment('Subscription Canceled Requested Date');
            $table->dateTime('stripe_ended_at')->nullable()->comment('Subscription Canceled Date i.e. Period End');

            $table->float('stripe_tax_rate')->nullable()->comment('Stripe Tax Rate Applied');
            $table->string('stripe_status', 150)->nullable()->comment('Stripe Subscription Status');

            $table->string('stripe_invoice_no', 150)->nullable()->comment('Stripe Invoice No');
            $table->string('stripe_invoice_reason', 150)->nullable()->comment('Stripe Invoice Billing Reason');
            $table->string('stripe_invoice_url', 500)->nullable()->comment('Stripe Invoice PDF URL');
            $table->string('stripe_invoice_status', 50)->nullable()->comment('Stripe Invoice Payment Status');
            $table->tinyInteger('stripe_invoice_is_paid')->default('0')->comment('Stripe Invoice Paid Flag');
            $table->float('stripe_invoice_amount')->default(0)->comment('Stripe Invoice Amount');
            $table->float('stripe_invoice_tax_amount')->default(0)->comment('Stripe Invoice Tax Amount');
            $table->dateTime('stripe_invoice_paid_at')->nullable()->comment('Stripe Invoice Payment Date');

            $table->string('applied_coupon', 150)->nullable();
            $table->text('stripe_response')->nullable();
            $table->tinyInteger('safety_flag')->default('0')->comment('Safety Flag when Switching Plans');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_subscriptions_details');
    }
}
