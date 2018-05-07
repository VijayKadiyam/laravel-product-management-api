<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bill_no');
            $table->integer('company_id');
            $table->integer('customer_id');
            $table->string('sub_total')->default(0);
            $table->integer('total_amount')->nullable();
            $table->boolean('gst_including');
            $table->string('delivery_note')->nullable();
            $table->string('delivery_note_date')->nullable();
            $table->string('supplier_reference')->nullable();
            $table->string('terms_of_payment')->nullable();
            $table->string('buyer_order_no')->nullable();
            $table->string('despatch_document_no')->nullable();
            $table->string('despatch_through')->nullable();
            $table->string('destination')->nullable();
            $table->string('terms_of_delivery')->nullable();
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
        Schema::dropIfExists('billings');
    }
}
