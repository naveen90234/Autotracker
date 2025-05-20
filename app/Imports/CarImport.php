<?php

namespace App\Imports;

use App\Models\Car;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CarImport implements ToModel, WithHeadingRow {
    public function model(array $row) {
        return new Car([
            'year' => $row['year'],
            'make' => $row['make'],
            'model' => $row['model'],
        ]);
    }
}
