<?php

namespace App\Imports;

use App\Models\Modalidad;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;


class ModalidadesImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        /*
        dd($row);
        */
        return new Modalidad([
            'aet'          => $row['aet'] ?? null,
            'ip'           => $row['ip'] ?? null,
            'model'        => $row['modelo'] ?? null,
            'centre'       => $row['centro'] ?? null,
            'location'     => $row['sala_localizacion'] ?? null,
            'department'   => $row['servicio'] ?? null,
            'observation'  => $row['observaciones'] ?? null,
            'syngo'        => $row['conectado_a_syngo'] ?? null,
            'modalidad'    => $row['modalidad'] ?? null,
            'machine'      => $row['maquina'] ?? null,
            'station'      => $row['station_name'] ?? null,
            'request_date' => $row['fecha_solicitud'] ?? null,
        ]);
    }


}
