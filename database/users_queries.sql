-- =====================================================================
-- REQUÊTES SQL POUR LA GESTION DES UTILISATEURS (TABLE `users`)
-- Projet : SeneBI
-- =====================================================================

-- ---------------------------------------------------------------------
-- 1. CRÉATION (INSERTION) D'UN UTILISATEUR
-- ---------------------------------------------------------------------

-- Exemple de création d'un utilisateur standard (en attente d'approbation)
-- Note : Le mot de passe inséré doit être hashé au préalable (ex: via bcrypt)
INSERT INTO users (
    name, 
    email, 
    password, 
    phone, 
    location, 
    company, 
    role, 
    saison, 
    is_active, 
    status, 
    created_at, 
    updated_at
) VALUES (
    'Adama Diop', 
    'adama.diop@example.com', 
    '$2y$12$e0Mzc2Mzc2Mzc2Mzc2Mzc2Our1O1gO9eW4H.Q6O2u7J5C9aV8vC2Jm', -- Exemple de hash bcrypt
    '+221771234567', 
    'Dakar', 
    'SeneAgri', 
    'client', -- Rôles possibles : 'client', 'manager', 'admin'
    '2026', 
    0, -- Non actif par défaut
    'pending', -- Statut initial : 'pending', 'approved', 'rejected'
    NOW(), 
    NOW()
);

-- Exemple de création directe d'un administrateur actif et approuvé
INSERT INTO users (
    name, 
    email, 
    password, 
    role, 
    is_active, 
    status, 
    approved_at,
    created_at, 
    updated_at
) VALUES (
    'Admin SeneBI', 
    'admin@senebi.com', 
    '$2y$12$e0Mzc2Mzc2Mzc2Mzc2Mzc2Our1O1gO9eW4H.Q6O2u7J5C9aV8vC2Jm', 
    'admin', 
    1, 
    'approved', 
    NOW(),
    NOW(), 
    NOW()
);


-- ---------------------------------------------------------------------
-- 2. MODIFICATION (MISE À JOUR) D'UN UTILISATEUR
-- ---------------------------------------------------------------------

-- a) Mise à jour des informations de profil de l'utilisateur
UPDATE users 
SET 
    name = 'Adama Diop Modifié',
    phone = '+221779998877',
    location = 'Thiès',
    company = 'SeneAgri SA',
    saison = '2027',
    updated_at = NOW()
WHERE id = :user_id; -- Remplacer :user_id par l'ID de l'utilisateur à modifier

-- b) Approbation d'un utilisateur par un administrateur
UPDATE users 
SET 
    status = 'approved',
    is_active = 1,
    approved_at = NOW(),
    approved_by = :admin_id, -- ID de l'administrateur qui approuve
    rejection_reason = NULL,
    rejected_at = NULL,
    updated_at = NOW()
WHERE id = :user_id;

-- c) Rejet d'une demande d'inscription d'un utilisateur
UPDATE users 
SET 
    status = 'rejected',
    is_active = 0,
    rejected_at = NOW(),
    rejection_reason = 'Documents requis non fournis ou invalides.',
    approved_at = NULL,
    approved_by = NULL,
    updated_at = NOW()
WHERE id = :user_id;

-- d) Modification du rôle d'un utilisateur
UPDATE users 
SET 
    role = 'manager', -- 'client', 'manager' ou 'admin'
    updated_at = NOW()
WHERE id = :user_id;

-- e) Désactivation / Activation manuelle d'un compte
UPDATE users 
SET 
    is_active = :new_status, -- 1 pour activer, 0 pour désactiver
    updated_at = NOW()
WHERE id = :user_id;


-- ---------------------------------------------------------------------
-- 3. SUPPRESSION D'UN UTILISATEUR
-- ---------------------------------------------------------------------

-- La table `users` utilise le Soft Deletes (suppression logique via le champ `deleted_at`)
-- pour éviter de supprimer définitivement les données liées à l'utilisateur.

-- a) Suppression logique (Soft Delete) - Recommandé
UPDATE users 
SET 
    deleted_at = NOW(),
    is_active = 0, -- Optionnel : désactiver également le compte
    updated_at = NOW()
WHERE id = :user_id AND deleted_at IS NULL;

-- b) Restauration d'un utilisateur supprimé logiquement
UPDATE users 
SET 
    deleted_at = NULL,
    is_active = 1, -- Réactiver le compte si nécessaire
    updated_at = NOW()
WHERE id = :user_id;

-- c) Suppression définitive (Hard Delete) - À utiliser avec précaution
-- Note : Si d'autres tables ont des clés étrangères liées à cet utilisateur sans ON DELETE CASCADE,
-- cela peut générer des erreurs d'intégrité référentielle.
DELETE FROM users 
WHERE id = :user_id;


-- ---------------------------------------------------------------------
-- 4. REQUÊTES DE SÉLECTION (LECTURE / READ)
-- ---------------------------------------------------------------------

-- a) Sélectionner un utilisateur spécifique par son ID (non supprimé)
SELECT * FROM users 
WHERE id = :user_id 
  AND deleted_at IS NULL;

-- b) Trouver un utilisateur par son adresse e-mail (ex: pour la connexion)
SELECT * FROM users 
WHERE email = :email 
  AND deleted_at IS NULL;

-- c) Lister tous les utilisateurs non supprimés (actifs et inactifs)
SELECT * FROM users 
WHERE deleted_at IS NULL 
ORDER BY created_at DESC;

-- d) Lister uniquement les utilisateurs actifs
SELECT * FROM users 
WHERE is_active = 1 
  AND deleted_at IS NULL;

-- e) Lister les demandes d'inscription en attente (status = 'pending')
SELECT * FROM users 
WHERE status = 'pending' 
  AND deleted_at IS NULL 
ORDER BY created_at ASC;

-- f) Lister les utilisateurs rejetés
SELECT * FROM users 
WHERE status = 'rejected' 
  AND deleted_at IS NULL;

-- g) Lister uniquement les utilisateurs supprimés logiquement (Soft Deleted)
SELECT * FROM users 
WHERE deleted_at IS NOT NULL;

-- h) Requête avec jointure (Self-Join) pour afficher l'utilisateur et l'administrateur qui l'a approuvé
SELECT 
    u.id AS user_id,
    u.name AS user_name,
    u.email AS user_email,
    u.role AS user_role,
    u.status AS user_status,
    u.approved_at,
    admin.id AS approver_id,
    admin.name AS approver_name,
    admin.email AS approver_email
FROM users u
LEFT JOIN users admin ON u.approved_by = admin.id
WHERE u.deleted_at IS NULL;

-- i) Statistiques globales sur les utilisateurs
SELECT 
    COUNT(*) AS total_utilisateurs,
    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS approuves,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS en_attente,
    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) AS rejetes,
    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) AS actifs,
    SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) AS inactifs,
    SUM(CASE WHEN deleted_at IS NOT NULL THEN 1 ELSE 0 END) AS supprimes_logiquement
FROM users;

