<?php
/**
 * Application Entry Point & Route Registry
 * Sistem Informasi Manajemen SPMI PPEPP
 * Universitas Katolik Soegijapranata (UNIKA Soegijapranata / SCU)
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Router.php';
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/AuditLogger.php';

// Instantiate Router
$router = new Router();

// ==========================================
// 1. PUBLIC ROUTES (GUEST / READ-ONLY)
// ==========================================
$router->get('', ['PublicController', 'index']);
$router->get('fakultas/{id:\d+}', ['PublicController', 'fakultasPpepp']);
$router->get('fakultas/{id:\d+}/ppepp', ['PublicController', 'fakultasPpepp']);
$router->get('prodi/{id:\d+}', ['PublicController', 'ppepp']);
$router->get('dokumen', ['PublicController', 'allDocuments']);
$router->get('tentang', ['PublicController', 'about']);

// ==========================================
// 2. AUTHENTICATION ROUTES
// ==========================================
$router->get('login', ['AuthController', 'showLogin']);
$router->post('login', ['AuthController', 'login']);
$router->get('logout', ['AuthController', 'logout']);

// ==========================================
// 3. SUPER ADMIN / ADMIN LPM ROUTES
// ==========================================
$router->get('admin/dashboard', ['SuperAdminController', 'dashboard']);

// Master Fakultas
$router->get('admin/fakultas', ['SuperAdminController', 'fakultas']);
$router->post('admin/fakultas/save', ['SuperAdminController', 'saveFakultas']);
$router->post('admin/fakultas/delete/{id}', ['SuperAdminController', 'deleteFakultas']);

// Master Program Studi
$router->get('admin/prodi', ['SuperAdminController', 'prodi']);
$router->post('admin/prodi/save', ['SuperAdminController', 'saveProdi']);
$router->post('admin/prodi/delete/{id}', ['SuperAdminController', 'deleteProdi']);

// Master Bidang Standar Mutu (Customizable by Admin LPM)
$router->get('admin/bidang', ['SuperAdminController', 'bidang']);
$router->post('admin/bidang/save', ['SuperAdminController', 'saveBidang']);
$router->post('admin/bidang/delete/{id}', ['SuperAdminController', 'deleteBidang']);

// Master Sub-Bidang Standar Mutu (CRUD per Bidang)
$router->post('admin/sub-bidang/save', ['SuperAdminController', 'saveSubBidang']);
$router->post('admin/sub-bidang/delete/{id}', ['SuperAdminController', 'deleteSubBidang']);

// Pusat Review & Verifikasi Dokumen Mutu (Admin LPM / Super Admin)
$router->get('admin/review', ['SuperAdminController', 'reviewHub']);
$router->get('admin/review-dokumen', ['SuperAdminController', 'reviewHub']);
$router->get('admin/monitoring-dokumen', ['SuperAdminController', 'monitoringDokumen']);
$router->get('admin/review-prodi/{id:\d+}', ['SuperAdminController', 'reviewProdi']);
$router->get('admin/review-fakultas/{id:\d+}', ['SuperAdminController', 'reviewFakultas']);
$router->post('admin/review-dokumen/submit', ['SuperAdminController', 'submitReview']);

// Manajemen Pengguna & Hak Akses
$router->get('admin/users', ['SuperAdminController', 'users']);
$router->post('admin/users/save', ['SuperAdminController', 'saveUser']);
$router->post('admin/users/delete/{id}', ['SuperAdminController', 'deleteUser']);

// Manajemen Konten Beranda Publik & Footer (LPM)
$router->get('admin/landing-settings', ['SuperAdminController', 'landingSettings']);
$router->post('admin/landing-settings/save', ['SuperAdminController', 'saveLandingSettings']);
$router->post('admin/landing-settings/reset', ['SuperAdminController', 'resetLandingSettings']);

// Profil & Foto Admin LPM
$router->get('admin/profile', ['ProfileController', 'index']);
$router->post('admin/profile/update', ['ProfileController', 'update']);

// ==========================================
// 4. ADMIN PROGRAM STUDI ROUTES
// ==========================================
$router->get('prodi/dashboard', ['AdminProdiController', 'dashboard']);

// Dokumen PPEPP CRUD + Soft Delete
$router->get('prodi/dokumen', ['AdminProdiController', 'documents']);
$router->get('prodi/perbaikan', ['AdminProdiController', 'perbaikan']);
$router->get('prodi/draft', ['AdminProdiController', 'draft']);
$router->get('prodi/dokumen/create', ['AdminProdiController', 'createDocument']);
$router->get('prodi/dokumen/edit/{id:\d+}', ['AdminProdiController', 'editDocument']);
$router->post('prodi/dokumen/save', ['AdminProdiController', 'saveDocument']);
$router->post('prodi/dokumen/ajukan/{id:\d+}', ['AdminProdiController', 'ajukanDraft']);
$router->post('prodi/dokumen/delete/{id:\d+}', ['AdminProdiController', 'softDeleteDocument']);
$router->post('prodi/dokumen/restore/{id:\d+}', ['AdminProdiController', 'restoreDocument']);
$router->post('prodi/dokumen/force-delete/{id:\d+}', ['AdminProdiController', 'forceDeleteDocument']);
$router->get('prodi/dokumen/arsip', ['AdminProdiController', 'arsip']);

// Profil Kaprodi
$router->get('prodi/kaprodi', ['AdminProdiController', 'kaprodi']);
$router->post('prodi/kaprodi/save', ['AdminProdiController', 'saveKaprodi']);

// Profil & Foto Admin Prodi
$router->get('prodi/profile', ['ProfileController', 'index']);
$router->post('prodi/profile/update', ['ProfileController', 'update']);

// ==========================================
// 5. ADMIN FAKULTAS ROUTES
// ==========================================
$router->get('fakultas/dashboard', ['AdminFakultasController', 'dashboard']);
$router->get('fakultas/dokumen', ['AdminFakultasController', 'documents']);
$router->get('fakultas/perbaikan', ['AdminFakultasController', 'perbaikan']);
$router->get('fakultas/draft', ['AdminFakultasController', 'draft']);
$router->get('fakultas/dokumen/create', ['AdminFakultasController', 'createDocument']);
$router->get('fakultas/dokumen/edit/{id:\d+}', ['AdminFakultasController', 'editDocument']);
$router->post('fakultas/dokumen/save', ['AdminFakultasController', 'saveDocument']);
$router->post('fakultas/dokumen/ajukan/{id:\d+}', ['AdminFakultasController', 'ajukanDraft']);
$router->post('fakultas/dokumen/delete/{id:\d+}', ['AdminFakultasController', 'softDeleteDocument']);
$router->post('fakultas/dokumen/restore/{id:\d+}', ['AdminFakultasController', 'restoreDocument']);
$router->post('fakultas/dokumen/force-delete/{id:\d+}', ['AdminFakultasController', 'forceDeleteDocument']);
$router->get('fakultas/dokumen/arsip', ['AdminFakultasController', 'arsip']);

// Profil Dekanat
$router->get('fakultas/dekanat', ['AdminFakultasController', 'dekanat']);
$router->post('fakultas/dekanat/save', ['AdminFakultasController', 'saveDekanat']);

// Profil & Foto Admin Fakultas
$router->get('fakultas/profile', ['ProfileController', 'index']);
$router->post('fakultas/profile/update', ['ProfileController', 'update']);

// ==========================================
// 6. GUGUS PENJAMINAN MUTU (GPM) ROUTES
// ==========================================
$router->get('gpm/dashboard', ['GpmController', 'dashboard']);
$router->get('gpm/rekapitulasi', ['GpmController', 'rekapitulasi']);
$router->get('gpm/dokumen', ['GpmController', 'documents']);
$router->get('gpm/perbaikan', ['GpmController', 'perbaikan']);
$router->get('gpm/draft', ['GpmController', 'draft']);
$router->get('gpm/dokumen/create', ['GpmController', 'createDocument']);
$router->get('gpm/dokumen/edit/{id:\d+}', ['GpmController', 'editDocument']);
$router->post('gpm/dokumen/save', ['GpmController', 'saveDocument']);
$router->post('gpm/dokumen/ajukan/{id:\d+}', ['GpmController', 'ajukanDraft']);
$router->post('gpm/dokumen/delete/{id:\d+}', ['GpmController', 'softDeleteDocument']);
$router->post('gpm/dokumen/restore/{id:\d+}', ['GpmController', 'restoreDocument']);
$router->post('gpm/dokumen/force-delete/{id:\d+}', ['GpmController', 'forceDeleteDocument']);
$router->get('gpm/dokumen/arsip', ['GpmController', 'arsip']);

// Profil & Foto GPM (Hanya Profil Akun, Tanpa Dekanat / Kaprodi)
$router->get('gpm/profile', ['ProfileController', 'index']);
$router->post('gpm/profile/update', ['ProfileController', 'update']);

// Redirect rute lama kepala/* ke admin/*
$router->get('kepala/dashboard', function() { redirect('admin/dashboard'); });
$router->get('kepala/rekapitulasi', function() { redirect('admin/rekapitulasi'); });
$router->get('kepala/audit', function() { redirect('admin/monitoring-dokumen'); });

// Dispatch Request
$router->dispatch();
