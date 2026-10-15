<?php

namespace App\Http\Controllers;

use App\Models\MJadwalPraktik;
use Illuminate\Http\Request;

class CJadwalPraktik extends Controller
{
    public function index()
    {
        $jadwals = MJadwalPraktik::with('dokter.user')
            ->orderBy('dokter_id')
            ->get();

        $jadwalGroups = $jadwals->groupBy('dokter_id')->map(function ($group) {
            $days = $group->pluck('hari')->unique()->values()->all();
            $orderedDays = array_values($days);
            $dayOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
            usort($orderedDays, function ($a, $b) use ($dayOrder) {
                return (array_search($a, $dayOrder, true) <=> array_search($b, $dayOrder, true));
            });

            $first = $group->first();

            return [
                'id' => $first->id,
                'dokter' => $first->dokter->user->name ?? '-',
                'hari' => $this->formatHariRange($orderedDays),
                'jam_mulai' => $first->jam_mulai,
                'jam_selesai' => $first->jam_selesai,
                'kuota' => $group->sum('kuota'),
            ];
        })->values();

        return view('admin.jadwal.index', compact('jadwalGroups'));
    }

    private function formatHariRange(array $days): string
    {
        $days = array_values(array_unique($days));

        if (empty($days)) {
            return '-';
        }

        if (count($days) === 1) {
            return $days[0];
        }

        $dayOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $ordered = collect($days)
            ->sortBy(fn ($day) => array_search($day, $dayOrder, true))
            ->values()
            ->all();

        $segments = [];
        $segment = [$ordered[0]];

        for ($i = 1; $i < count($ordered); $i++) {
            $current = array_search($ordered[$i], $dayOrder, true);
            $previous = array_search($ordered[$i - 1], $dayOrder, true);

            if ($current === $previous + 1) {
                $segment[] = $ordered[$i];
                continue;
            }

            $segments[] = $segment;
            $segment = [$ordered[$i]];
        }

        $segments[] = $segment;

        $formatted = [];
        foreach ($segments as $segmentDays) {
            if (count($segmentDays) === 1) {
                $formatted[] = $segmentDays[0];
                continue;
            }

            $formatted[] = $segmentDays[0] . ' - ' . $segmentDays[count($segmentDays) - 1];
        }

        return implode(', ', $formatted);
    }

    public function store(Request $request)
    {
        $request->validate([
            'dokter_id'   => 'required|exists:dokters,id',
            'hari'        => 'required|string',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required',
            'kuota'       => 'required|integer|min:1',
        ]);

        MJadwalPraktik::create($request->all());
        return back()->with('success', 'Jadwal Praktik berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'dokter_id'   => 'required|exists:dokters,id',
            'hari'        => 'required|string',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required',
            'kuota'       => 'required|integer|min:1',
        ]);

        $jadwal = MJadwalPraktik::findOrFail($id);
        $jadwal->update($request->only(['dokter_id', 'hari', 'jam_mulai', 'jam_selesai', 'kuota']));

        return back()->with('success', 'Jadwal Praktik berhasil diubah.');
    }

    public function destroy($id)
    {
        MJadwalPraktik::findOrFail($id)->delete();
        return back()->with('success', 'Jadwal Praktik berhasil dihapus.');
    }
}