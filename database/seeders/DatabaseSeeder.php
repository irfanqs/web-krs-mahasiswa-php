<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $hash = '$2y$10$.yMstSlK7t3WIZHXnQHjDOfcB/L8ul2o9231s0fymVPPRDKx3Adri'; // password123

        DB::table('admin')->insert([
            ['username' => 'admin', 'password' => $hash, 'nama' => 'Administrator'],
        ]);

        DB::table('dosen')->insert([
            ['nidn' => '198504122010', 'nama' => 'Dr. Ahmad Fauzi',     'email' => 'ahmad@university.ac.id',   'password' => $hash, 'jurusan' => 'Teknik Informatika'],
            ['nidn' => '197803052008', 'nama' => 'Dr. Sari Dewi',       'email' => 'sari@university.ac.id',    'password' => $hash, 'jurusan' => 'Sistem Informasi'],
            ['nidn' => '198210302011', 'nama' => 'Prof. Budi Santoso',  'email' => 'budi.s@university.ac.id',  'password' => $hash, 'jurusan' => 'Teknik Informatika'],
            ['nidn' => '199001152015', 'nama' => 'Dr. Rina Wahyuni',    'email' => 'rina@university.ac.id',    'password' => $hash, 'jurusan' => 'Sistem Informasi'],
            ['nidn' => '196912052000', 'nama' => 'Prof. Hendra Gunawan','email' => 'hendra@university.ac.id',  'password' => $hash, 'jurusan' => 'Teknik Informatika'],
        ]);

        DB::table('mahasiswa')->insert([
            ['nim' => '21010023', 'nama' => 'Budi Prasetyo',   'email' => 'budi@student.ac.id',  'password' => $hash, 'angkatan' => 2021, 'program_studi' => 'Teknik Informatika', 'status' => 'aktif'],
            ['nim' => '21010045', 'nama' => 'Siti Rahayu',     'email' => 'siti@student.ac.id',  'password' => $hash, 'angkatan' => 2021, 'program_studi' => 'Teknik Informatika', 'status' => 'aktif'],
            ['nim' => '22010012', 'nama' => 'Agus Salim',      'email' => 'agus@student.ac.id',  'password' => $hash, 'angkatan' => 2022, 'program_studi' => 'Sistem Informasi',   'status' => 'aktif'],
            ['nim' => '22010034', 'nama' => 'Dewi Kusuma',     'email' => 'dewi@student.ac.id',  'password' => $hash, 'angkatan' => 2022, 'program_studi' => 'Sistem Informasi',   'status' => 'aktif'],
            ['nim' => '20010078', 'nama' => 'Fajar Nugraha',   'email' => 'fajar@student.ac.id', 'password' => $hash, 'angkatan' => 2020, 'program_studi' => 'Teknik Informatika', 'status' => 'aktif'],
            ['nim' => '20010056', 'nama' => 'Hani Putri',      'email' => 'hani@student.ac.id',  'password' => $hash, 'angkatan' => 2020, 'program_studi' => 'Teknik Informatika', 'status' => 'lulus'],
            ['nim' => '21020067', 'nama' => 'Rizky Ramadan',   'email' => 'rizky@student.ac.id', 'password' => $hash, 'angkatan' => 2021, 'program_studi' => 'Sistem Informasi',   'status' => 'aktif'],
            ['nim' => '22020089', 'nama' => 'Nurul Hidayah',   'email' => 'nurul@student.ac.id', 'password' => $hash, 'angkatan' => 2022, 'program_studi' => 'Sistem Informasi',   'status' => 'aktif'],
            ['nim' => '23010001', 'nama' => 'Andi Firmansyah', 'email' => 'andi@student.ac.id',  'password' => $hash, 'angkatan' => 2023, 'program_studi' => 'Teknik Informatika', 'status' => 'aktif'],
            ['nim' => '23010002', 'nama' => 'Citra Lestari',   'email' => 'citra@student.ac.id', 'password' => $hash, 'angkatan' => 2023, 'program_studi' => 'Teknik Informatika', 'status' => 'cuti'],
        ]);

        DB::table('mata_kuliah')->insert([
            ['kode_matkul' => 'TIK-301', 'nama_matkul' => 'Pemrograman Web',              'sks' => 3, 'semester' => 5, 'jenis' => 'wajib'],
            ['kode_matkul' => 'TIK-302', 'nama_matkul' => 'Basis Data Lanjut',            'sks' => 3, 'semester' => 5, 'jenis' => 'wajib'],
            ['kode_matkul' => 'TIK-303', 'nama_matkul' => 'Kecerdasan Buatan',            'sks' => 3, 'semester' => 5, 'jenis' => 'wajib'],
            ['kode_matkul' => 'TIK-304', 'nama_matkul' => 'Jaringan Komputer',            'sks' => 3, 'semester' => 5, 'jenis' => 'wajib'],
            ['kode_matkul' => 'TIK-305', 'nama_matkul' => 'Rekayasa Perangkat Lunak',     'sks' => 3, 'semester' => 5, 'jenis' => 'wajib'],
            ['kode_matkul' => 'TIK-401', 'nama_matkul' => 'Keamanan Sistem',              'sks' => 3, 'semester' => 7, 'jenis' => 'wajib'],
            ['kode_matkul' => 'TIK-402', 'nama_matkul' => 'Machine Learning',             'sks' => 3, 'semester' => 7, 'jenis' => 'pilihan'],
            ['kode_matkul' => 'TIK-403', 'nama_matkul' => 'Cloud Computing',              'sks' => 3, 'semester' => 7, 'jenis' => 'pilihan'],
            ['kode_matkul' => 'TIK-404', 'nama_matkul' => 'Pemrograman Mobile',           'sks' => 3, 'semester' => 7, 'jenis' => 'pilihan'],
            ['kode_matkul' => 'TIK-405', 'nama_matkul' => 'Sistem Terdistribusi',         'sks' => 3, 'semester' => 7, 'jenis' => 'wajib'],
            ['kode_matkul' => 'SIF-301', 'nama_matkul' => 'Analisis Sistem Informasi',    'sks' => 3, 'semester' => 5, 'jenis' => 'wajib'],
            ['kode_matkul' => 'SIF-302', 'nama_matkul' => 'Enterprise Architecture',      'sks' => 3, 'semester' => 5, 'jenis' => 'pilihan'],
            ['kode_matkul' => 'TIK-201', 'nama_matkul' => 'Struktur Data',                'sks' => 3, 'semester' => 3, 'jenis' => 'wajib'],
            ['kode_matkul' => 'TIK-202', 'nama_matkul' => 'Algoritma Pemrograman',        'sks' => 3, 'semester' => 3, 'jenis' => 'wajib'],
            ['kode_matkul' => 'TIK-106', 'nama_matkul' => 'Kalkulus',                     'sks' => 2, 'semester' => 1, 'jenis' => 'wajib'],
        ]);

        DB::table('semester')->insert([
            ['tahun_ajaran' => '2022/2023', 'tingkatan_semester' => 'ganjil', 'status' => 'nonaktif'],
            ['tahun_ajaran' => '2022/2023', 'tingkatan_semester' => 'genap',  'status' => 'nonaktif'],
            ['tahun_ajaran' => '2023/2024', 'tingkatan_semester' => 'ganjil', 'status' => 'nonaktif'],
            ['tahun_ajaran' => '2023/2024', 'tingkatan_semester' => 'genap',  'status' => 'aktif'],
        ]);

        // Semester 1 (2022/2023 ganjil) - previous semester jadwal
        DB::table('jadwal_kuliah')->insert([
            ['id_matkul' => 13, 'id_dosen' => '198504122010', 'id_semester' => 1, 'hari' => 'Senin',  'jam_mulai' => '08:00:00', 'jam_selesai' => '10:40:00', 'ruang' => 'R.301', 'kuota' => 35],
            ['id_matkul' => 14, 'id_dosen' => '197803052008', 'id_semester' => 1, 'hari' => 'Selasa', 'jam_mulai' => '08:00:00', 'jam_selesai' => '10:40:00', 'ruang' => 'R.302', 'kuota' => 35],
        ]);

        // Semester 4 (2023/2024 genap - aktif) - current semester jadwal
        DB::table('jadwal_kuliah')->insert([
            ['id_matkul' => 1,  'id_dosen' => '198504122010', 'id_semester' => 4, 'hari' => 'Senin',  'jam_mulai' => '08:00:00', 'jam_selesai' => '10:40:00', 'ruang' => 'R.401', 'kuota' => 35],
            ['id_matkul' => 2,  'id_dosen' => '197803052008', 'id_semester' => 4, 'hari' => 'Selasa', 'jam_mulai' => '10:00:00', 'jam_selesai' => '12:40:00', 'ruang' => 'Lab A', 'kuota' => 30],
            ['id_matkul' => 3,  'id_dosen' => '198210302011', 'id_semester' => 4, 'hari' => 'Rabu',   'jam_mulai' => '08:00:00', 'jam_selesai' => '10:40:00', 'ruang' => 'R.402', 'kuota' => 35],
            ['id_matkul' => 4,  'id_dosen' => '199001152015', 'id_semester' => 4, 'hari' => 'Kamis',  'jam_mulai' => '13:00:00', 'jam_selesai' => '15:40:00', 'ruang' => 'Lab B', 'kuota' => 30],
            ['id_matkul' => 5,  'id_dosen' => '196912052000', 'id_semester' => 4, 'hari' => 'Jumat',  'jam_mulai' => '08:00:00', 'jam_selesai' => '10:40:00', 'ruang' => 'R.403', 'kuota' => 35],
            ['id_matkul' => 6,  'id_dosen' => '198504122010', 'id_semester' => 4, 'hari' => 'Senin',  'jam_mulai' => '13:00:00', 'jam_selesai' => '15:40:00', 'ruang' => 'R.404', 'kuota' => 30],
            ['id_matkul' => 7,  'id_dosen' => '197803052008', 'id_semester' => 4, 'hari' => 'Rabu',   'jam_mulai' => '13:00:00', 'jam_selesai' => '15:40:00', 'ruang' => 'R.401', 'kuota' => 25],
            ['id_matkul' => 8,  'id_dosen' => '198210302011', 'id_semester' => 4, 'hari' => 'Selasa', 'jam_mulai' => '13:00:00', 'jam_selesai' => '15:40:00', 'ruang' => 'Lab C', 'kuota' => 25],
            ['id_matkul' => 9,  'id_dosen' => '199001152015', 'id_semester' => 4, 'hari' => 'Kamis',  'jam_mulai' => '08:00:00', 'jam_selesai' => '10:40:00', 'ruang' => 'Lab A', 'kuota' => 25],
            ['id_matkul' => 10, 'id_dosen' => '196912052000', 'id_semester' => 4, 'hari' => 'Jumat',  'jam_mulai' => '13:00:00', 'jam_selesai' => '15:40:00', 'ruang' => 'R.402', 'kuota' => 30],
        ]);

        // KRS - previous semester (jadwal id 1-2)
        DB::table('krs')->insert([
            ['id_mahasiswa' => '21010023', 'id_jadwal' => 1, 'tanggal_ambil' => '2022-09-05 10:30:00'],
            ['id_mahasiswa' => '21010023', 'id_jadwal' => 2, 'tanggal_ambil' => '2022-09-05 10:35:00'],
        ]);

        // KRS - current semester (jadwal id 3-12 → rows 3-7 = id_jadwal 3,4,5,7 in current semester)
        DB::table('krs')->insert([
            ['id_mahasiswa' => '21010023', 'id_jadwal' => 3, 'tanggal_ambil' => '2024-02-15 09:00:00'],
            ['id_mahasiswa' => '21010023', 'id_jadwal' => 4, 'tanggal_ambil' => '2024-02-15 09:05:00'],
            ['id_mahasiswa' => '21010023', 'id_jadwal' => 5, 'tanggal_ambil' => '2024-02-15 09:10:00'],
            ['id_mahasiswa' => '21010023', 'id_jadwal' => 7, 'tanggal_ambil' => '2024-02-15 09:15:00'],
        ]);

        // Nilai - previous semester (id_krs 1-2, status_kunci=1)
        DB::table('nilai')->insert([
            ['id_krs' => 1, 'tugas' => 85.00, 'uts' => 80.00, 'uas' => 88.00, 'nilai_angka' => 86.50, 'nilai_huruf' => 'A',  'status_kunci' => 1],
            ['id_krs' => 2, 'tugas' => 78.00, 'uts' => 75.00, 'uas' => 80.00, 'nilai_angka' => 78.50, 'nilai_huruf' => 'B+', 'status_kunci' => 1],
        ]);

        DB::table('pengumuman')->insert([
            ['judul' => 'Pengisian KRS Semester Genap 2023/2024 Dibuka',   'isi' => 'Sistem pengisian KRS sudah dibuka. Mahasiswa dapat melakukan pengisian KRS sampai dengan tanggal 28 Februari 2024.', 'tipe' => 'ACADEMIC', 'created_at' => now(), 'updated_at' => now()],
            ['judul' => 'Jadwal UTS Semester Genap 2023/2024',             'isi' => 'Ujian Tengah Semester akan dilaksanakan pada bulan Maret 2024. Detail jadwal akan diumumkan lebih lanjut.',          'tipe' => 'ACADEMIC', 'created_at' => now(), 'updated_at' => now()],
            ['judul' => 'Beasiswa Prestasi Akademik 2024 Dibuka',          'isi' => 'Pendaftaran beasiswa prestasi akademik tahun 2024 sudah dibuka. Silakan kunjungi portal beasiswa untuk informasi lebih lanjut.', 'tipe' => 'EVENT', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
