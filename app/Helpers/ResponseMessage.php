<?php

namespace App\Helpers;

class ResponseMessage
{
    // Success Messages
    const INFRASTRUCTURE_CREATED = 'Aset baru berhasil didaftarkan.';
    const INFRASTRUCTURE_UPDATED = 'Data aset berhasil diperbarui.';
    const INFRASTRUCTURE_DELETED = 'Aset berhasil dihapus dari sistem.';
    const INFRASTRUCTURE_DELETED_WITH_FILE = 'Aset dan fotonya berhasil dihapus.';

    const BREAKDOWN_CREATED = 'Laporan kerusakan berhasil dicatat. Status aset kini Breakdown.';
    const BREAKDOWN_UPDATED = 'Status progress & data tanggal berhasil diperbarui.';
    const BREAKDOWN_RESOLVED = 'Pekerjaan selesai! Aset telah kembali beroperasi (Ready).';
    const BREAKDOWN_DELETED = 'Laporan dan lampirannya berhasil dihapus.';

    const ENTITY_CREATED = 'Entitas pelabuhan baru berhasil didaftarkan.';
    const ENTITY_UPDATED = 'Data entitas berhasil diperbarui.';
    const ENTITY_DELETED = 'Entitas pelabuhan berhasil dihapus.';
    const ENTITY_HAS_INFRASTRUCTURE = 'Gagal dihapus! Entitas masih memiliki data infrastruktur terdaftar.';

    const USER_CREATED = 'Akun operator berhasil didaftarkan.';
    const USER_UPDATED = 'Data akun berhasil diperbarui.';
    const USER_DELETED = 'Akun berhasil dihapus.';
    const USER_CANNOT_DELETE_SELF = 'Anda tidak bisa menghapus akun yang sedang Anda gunakan.';

    // Error Messages
    const UNAUTHORIZED_ACCESS = 'Akses Ditolak: Anda tidak memiliki hak akses untuk aksi ini.';
    const UNAUTHORIZED_OTHER_BRANCH = 'Akses ditolak! Ini bukan aset di wilayah Anda.';
    const UNAUTHORIZED_BREAKDOWN_OTHER_BRANCH = 'Anda tidak bisa melakukan aksi pada laporan dari cabang lain.';
    const UNAUTHORIZED_SUPERADMIN = 'Akses Ditolak: Anda tidak memiliki hak akses Super Admin.';

    const INFRASTRUCTURE_NOT_FOUND = 'Aset tidak ditemukan.';
    const BREAKDOWN_NOT_FOUND = 'Laporan kerusakan tidak ditemukan.';
    const ENTITY_NOT_FOUND = 'Entitas tidak ditemukan.';
    const USER_NOT_FOUND = 'Pengguna tidak ditemukan.';

    // Validation Messages
    const ENTITY_REQUIRED_FOR_SUPERADMIN = 'Entitas/Cabang wajib dipilih untuk Superadmin!';
    const INFRASTRUCTURE_TYPE_REQUIRED = 'Jenis alat wajib diisi!';
}
