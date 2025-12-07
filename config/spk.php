<?php

return [
    // Toggle seluruh fitur SPK. Set ENV SPK_AUTO_RANK_ENABLED=false untuk menonaktifkan.
    'auto_rank_enabled' => env('SPK_AUTO_RANK_ENABLED', true),

    // Bobot dan parameter untuk perhitungan SAW otomatis tanpa input manual.
    'auto_rank' => [
        // Bobot kriteria (total 1.0)
        'weights' => [
            'rpjmd_relevance' => 0.30,   // Relevansi dengan prioritas RPJMD
            'urgency' => 0.20,           // Kata kunci bencana/kesehatan, dsb.
            'completeness' => 0.20,      // Kelengkapan dokumen wajib
            'impact' => 0.15,            // Dampak ke masyarakat (berdasarkan bidang/topik)
            'procedure' => 0.15,         // Kesesuaian prosedur (format/jadwal)
        ],

        // Kata kunci prioritas RPJMD
        'rpjmd_keywords' => [
            'kemiskinan', 'kesehatan', 'stunting', 'bencana', 'iklim', 'lingkungan', 'pendidikan',
            'ketahanan pangan', 'ekonomi kerakyatan', 'umkm', 'infrastruktur', 'energi',
        ],

        // Kata kunci urgensi (kasus darurat/strategis)
        'urgency_keywords' => [
            'bencana', 'pandemi', 'wabah', 'gawat', 'krisis', 'darurat', 'stunting', 'kematian',
            'rawan', 'banjir', 'longsor', 'kebakaran', 'inflasi', 'gizi buruk',
        ],

        // Mapping dampak berdasarkan bidang/topik (lowercase substring => skor 0-1)
        'impact_mapping' => [
            'kesehatan' => 1.0,
            'bencana' => 0.95,
            'kemiskinan' => 0.9,
            'ekonomi' => 0.8,
            'pendidikan' => 0.75,
            'lingkungan' => 0.7,
            'pertanian' => 0.65,
            'teknologi' => 0.6,
            'umkm' => 0.6,
        ],

        // Dokumen yang dicek (nama => bobot parsial, total 1.0)
        'documents' => [
            'proposal' => 0.35,          // diwakili oleh pdf_path
            'surat_permohonan' => 0.35,  // diwakili oleh kesbang_letter_path
            'ktp' => 0.15,               // belum tersedia di model, nilainya 0 jika tak ada
            'surat_rekomendasi' => 0.15, // belum tersedia di model, nilainya 0 jika tak ada
        ],

        // Skor status (approval)
        'status_scores' => [
            'approved' => 1.0,
            'kesbang_verified' => 0.85,
            'submitted' => 0.7,
            'draft' => 0.4,
            'rejected' => 0.2,
        ],

        // Valid ekstensi untuk prosedur/format
        'allowed_pdf_extensions' => ['pdf'],
        'allowed_image_extensions' => ['jpg', 'jpeg', 'png'],
    ],
];
