<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Stock;

class SeedTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:seed-test-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Команда для заполнения справочников товаров, складов и остатков тестовыми данными';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Массивы с тестовыми данными
        $products = [
            ['name' => 'Товар 1', 'price' => 1000.23],
            ['name' => 'Товар 2', 'price' => 200.10],
            ['name' => 'Товар 3', 'price' => 130.99],
            ['name' => 'Товар 4', 'price' => 900.20],
            ['name' => 'Товар 5', 'price' => 300.50],
        ];

        $warehouses = [
            ['name' => 'Склад 1'],
            ['name' => 'Склад 2'],
            ['name' => 'Склад 3'],
        ];

        $stocks = [
            ['product_id' => 1, 'warehouse_id' => 1, 'stock' => 500],
            ['product_id' => 1, 'warehouse_id' => 2, 'stock' => 100],
            ['product_id' => 1, 'warehouse_id' => 3, 'stock' => 0],
            ['product_id' => 2, 'warehouse_id' => 1, 'stock' => 650],
            ['product_id' => 2, 'warehouse_id' => 2, 'stock' => 900],
            ['product_id' => 2, 'warehouse_id' => 3, 'stock' => 10],
            ['product_id' => 3, 'warehouse_id' => 1, 'stock' => 0],
            ['product_id' => 3, 'warehouse_id' => 2, 'stock' => 50],
            ['product_id' => 3, 'warehouse_id' => 3, 'stock' => 200],
            ['product_id' => 4, 'warehouse_id' => 1, 'stock' => 0],
            ['product_id' => 4, 'warehouse_id' => 2, 'stock' => 10],
            ['product_id' => 4, 'warehouse_id' => 3, 'stock' => 100],
            ['product_id' => 5, 'warehouse_id' => 1, 'stock' => 50],
            ['product_id' => 5, 'warehouse_id' => 2, 'stock' => 300],
            ['product_id' => 5, 'warehouse_id' => 3, 'stock' => 150],
        ];

        // Сидинг товаров
        foreach ($products as $product) {
            Product::create($product);
        }

        // Сидинг складов
        foreach ($warehouses as $warehouse) {
            Warehouse::create($warehouse);
        }

        // Сидинг остатков
        foreach ($stocks as $stock) {
            Stock::create($stock);
        }

        $this->info('Тестовые данные добавлены.');

        return 0;
    }
}
