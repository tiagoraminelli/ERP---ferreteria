<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 200)->index();
            $table->text('descripcion')->nullable();
            $table->string('codigo_barra', 50)->nullable()->index();
            $table->string('modelo', 100)->nullable();

            $table->unsignedBigInteger('categoria_id')->nullable()->index();
            $table->unsignedBigInteger('marca_id')->nullable()->index();
            $table->unsignedBigInteger('unidad_medida_id')->default(1)->index();
            $table->unsignedBigInteger('proveedor_id')->nullable()->index();

            $table->decimal('precio', 10, 2)->default(0.00);
            $table->decimal('precio_costo', 10, 2)->nullable()->default(0.00);
            $table->decimal('margen_ganancia', 5, 2)->nullable()->default(30.00);

            $table->decimal('stock', 10, 3)->default(0.000)->index();
            $table->string('unidad_medida', 20)->nullable()->default('unid');
            $table->decimal('stock_minimo', 10, 3)->default(0.000);

            $table->string('ubicacion_deposito', 50)->nullable();
            $table->string('imagen', 255)->nullable();

            $table->boolean('activo')->default(true);

            $table->softDeletes(); // deleted_at
            $table->timestamps();  // created_at y updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
