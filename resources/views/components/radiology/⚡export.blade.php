<?php

use Livewire\Component;

new class extends Component
{
    public function export()
    {
        $fileName = 'modalidades_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $modalidades = Modalidad::query()
            ->when(trim($this->search) !== '', function ($query) {

                $search = '%' . trim($this->search) . '%';

                $query->where(function ($q) use ($search) {
                    $q->where('aet', 'like', $search)
                      ->orWhere('ip', 'like', $search)
                      ->orWhere('location', 'like', $search)
                      ->orWhere('department', 'like', $search);
                });
            })
            ->orderByRaw('LOWER(' . $this->sortBy . ') ' . $this->sortDirection)
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ];

        $callback = function () use ($modalidades) {

            $file = fopen('php://output', 'w');

            // BOM UTF-8 para Excel Windows
            fwrite($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'AET',
                'IP',
                'Ubicación',
                'Departamento',
                'Fecha creación',
            ], ';');

            foreach ($modalidades as $modalidad) {
                fputcsv($file, [
                    $modalidad->aet,
                    $modalidad->ip,
                    $modalidad->location,
                    $modalidad->department,
                    optional($modalidad->created_at)->format('d/m/Y H:i'),
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
};
?>

<div>
    <flux:button wire:click="export">Exportar</flux:button>
</div>