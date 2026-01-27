public function up()
{
    Schema::table('orders', function (Blueprint $table) {
        $table->string('payment_method')->default('cash')->after('status');
        $table->string('payment_status')->default('pending')->after('payment_method'); 
        // pending/paid/failed (opsionale)
    });
}

public function down()
{
    Schema::table('orders', function (Blueprint $table) {
        $table->dropColumn(['payment_method','payment_status']);
    });
}
