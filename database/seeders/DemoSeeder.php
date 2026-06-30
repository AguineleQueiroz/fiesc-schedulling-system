<?php

namespace Database\Seeders;

use App\Enums\AppointmentStatus;
use App\Enums\UserRole;
use App\Models\Appointment;
use App\Models\Availability;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $attendant = User::firstOrCreate(
            ['email' => 'atendente@fiesc.local'],
            [
                'name'     => 'Ana Oliveira',
                'password' => Hash::make('atendente1234'),
                'role'     => UserRole::Attendant,
            ]
        );

        /**
         * Disponibilidade: seg–sex, 08:00–12:00 e 13:00–17:00
         */
        $windows = [
            ['start' => '08:00', 'end' => '12:00'],
            ['start' => '13:00', 'end' => '17:00'],
        ];

        foreach (range(1, 5) as $dayOfWeek) { // 1=seg … 5=sex
            foreach ($windows as $window) {
                Availability::firstOrCreate(
                    [
                        'user_id'    => $attendant->id,
                        'day_of_week' => $dayOfWeek,
                        'start_time' => $window['start'],
                        'end_time'   => $window['end'],
                    ],
                    ['active' => true]
                );
            }
        }

        /**
         * Agendamentos: semana corrente (seg–sex)
         */
        $monday = Carbon::now()->startOfWeek(Carbon::MONDAY);

        $appointments = [
            [
                'offset'       => 0, // segunda
                'start_time'   => '08:00',
                'end_time'     => '08:30',
                'client_name'  => 'Carlos Mendes',
                'client_phone' => '(47) 99811-2233',
            ],
            [
                'offset'       => 0, // segunda
                'start_time'   => '09:00',
                'end_time'     => '09:30',
                'client_name'  => 'Fernanda Lima',
                'client_phone' => '(47) 98722-4455',
            ],
            [
                'offset'       => 1, // terça
                'start_time'   => '13:00',
                'end_time'     => '13:30',
                'client_name'  => 'Roberto Souza',
                'client_phone' => '(47) 99633-6677',
            ],
            [
                'offset'       => 2, // quarta
                'start_time'   => '10:00',
                'end_time'     => '10:30',
                'client_name'  => 'Patrícia Costa',
                'client_phone' => '(47) 98544-8899',
            ],
            [
                'offset'       => 3, // quinta
                'start_time'   => '14:00',
                'end_time'     => '14:30',
                'client_name'  => 'Marcos Alves',
                'client_phone' => '(47) 99455-0011',
            ],
        ];

        foreach ($appointments as $data) {
            $date = $monday->copy()->addDays($data['offset'])->format('Y-m-d');

            Appointment::firstOrCreate(
                [
                    'attendant_id' => $attendant->id,
                    'date'         => $date,
                    'start_time'   => $data['start_time'],
                ],
                [
                    'end_time'     => $data['end_time'],
                    'client_name'  => $data['client_name'],
                    'client_phone' => $data['client_phone'],
                    'status'       => AppointmentStatus::Scheduled,
                ]
            );
        }
    }
}
