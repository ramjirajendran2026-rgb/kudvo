@php
    /** @var \App\Models\Election $election */
@endphp

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <title>Ballot - {{ $election->name }}</title>

        <style>
            .page {
                padding: 10mm;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }

            table,
            th,
            td {
                border: 0.2mm solid;
            }

            th,
            td {
                padding: 2mm;
                vertical-align: middle;
                width: auto;
            }

            .pos-tbl {
                margin-top: 10mm;
            }
        </style>
    </head>
    <body>
        <footer style="position: fixed; bottom: 0; left: 0; right: 0; text-align: center; padding: 6mm">
            Powered by {{ config('app.name') }}
        </footer>
        <div class="page">
            <table>
                <thead>
                    <tr>
                        <th colspan="2" class="og-title">
                            <img
                                src="{{ $election->organisation->logo_url }}"
                                alt="{{ 'Logo' }}"
                                style="height: 20mm"
                            />
                            <div style="vertical-align: middle; text-align: center; margin-top: 2mm">
                                {{ $election->organisation->name }}
                            </div>
                        </th>
                        <th rowspan="3" style="width: 10mm">
                            <img
                                alt="Ballot QR code"
                                src="data:image/svg+xml;base64,{!! base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::size(100)->generate($election->code . '|' . $elector->uuid)) !!}"
                            />
                        </th>
                    </tr>
                    <tr>
                        <th colspan="2" class="el-title">{{ $election->name }}</th>
                    </tr>
                    <tr>
                        <td align="center">
                            {{ $election->starts_at_local?->format('h:i A (T)') }}
                            <br />
                            {{ $election->starts_at_local?->toFormattedDateString() }}
                        </td>
                        <td align="center">
                            {{ $election->ends_at_local?->format('h:i A (T)') }}
                            <br />
                            {{ $election->ends_at_local?->toFormattedDateString() }}
                        </td>
                    </tr>
                </thead>
            </table>

            @foreach ($election->positions as $position)
                <table class="pos-tbl">
                    <thead>
                        <tr>
                            <th colspan="3">
                                {{ $position->name }}
                                <br />
                                <small>
                                    {{ str(string: $position->quota . ' post')->plural(count: $position->quota) }}
                                </small>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($votes[$position->uuid] as $vote)
                            @php
                                $candidate = $position->candidates->firstWhere('uuid', $vote->key);
                            @endphp

                            <tr>
                                <td style="height: 16mm; width: 16mm">
                                    <img
                                        src="{{ $candidate->photo_url }}"
                                        alt="{{ 'Candidate photo' }}"
                                        style="border-radius: 100%; height: 15mm; width: 15mm"
                                    />
                                </td>
                                <td>
                                    <div>{{ $candidate->full_name }}</div>
                                    <div>
                                        @php
                                            $contacts = \Illuminate\Support\Arr::where(
                                                $candidate->only(['membership_number', 'phone', 'email']),
                                                fn ($item) => filled($item),
                                            );
                                        @endphp

                                        {{ implode(' • ', $contacts) }}
                                    </div>
                                </td>
                                <td style="height: 16mm; width: 16mm">
                                    <img
                                        src="{{ $candidate->symbol_url }}"
                                        alt="{{ 'Candidate symbol' }}"
                                        style="border-radius: 1mm; height: 15mm; width: 15mm"
                                    />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="padding: 6mm; text-align: center; vertical-align: middle">
                                    None selected
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @endforeach
        </div>
    </body>
</html>
