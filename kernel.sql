-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 10, 2025 at 08:49 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kernel`
--

-- --------------------------------------------------------

--
-- Table structure for table `actualite`
--

CREATE TABLE `actualite` (
  `id` int(11) NOT NULL,
  `titre` varchar(200) NOT NULL,
  `contenu` text NOT NULL,
  `date_publication` datetime DEFAULT current_timestamp(),
  `type` enum('milestone','update','announcement') DEFAULT 'update',
  `projet_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `actualite`
--

INSERT INTO `actualite` (`id`, `titre`, `contenu`, `date_publication`, `type`, `projet_id`) VALUES
(1, 'Lancement de la version Beta', 'Nous sommes heureux d\'annoncer le lancement de notre version Beta ! Les premiers tests sont très prometteurs et nous avons reçu d\'excellents retours de nos beta-testeurs. La version finale est prévue pour le mois prochain.', '2025-11-15 10:30:00', 'milestone', 1),
(2, 'Mise à jour de sécurité v1.2', 'Nous avons déployé une mise à jour importante qui corrige plusieurs failles de sécurité et améliore significativement les performances. Le temps de réponse a été réduit de 40%.', '2025-11-18 14:15:00', 'update', 1),
(3, 'Nouveau partenariat stratégique', 'Nous avons signé un partenariat avec une grande entreprise du secteur technologique. Ce partenariat nous permettra d\'accélérer notre développement et d\'atteindre de nouveaux marchés.', '2025-11-19 09:00:00', 'announcement', 1),
(4, 'Installation du premier prototype', 'Le premier prototype de notre système domotique a été installé avec succès dans une maison témoin. Les résultats préliminaires montrent une réduction de 35% de la consommation énergétique.', '2025-11-10 11:00:00', 'milestone', 2),
(5, 'Ajout de nouveaux capteurs IoT', 'Nous avons intégré de nouveaux capteurs de température et d\'humidité pour un contrôle encore plus précis de l\'environnement intérieur.', '2025-11-17 16:45:00', 'update', 2),
(6, 'Lancement de la marketplace', 'Notre marketplace NFT est maintenant en ligne ! Les artistes tunisiens peuvent dès maintenant créer et vendre leurs œuvres numériques. Plus de 50 artistes se sont déjà inscrits.', '2025-11-12 13:20:00', 'milestone', 3),
(7, 'Réduction des frais de transaction', 'Grâce à notre nouvelle infrastructure, nous avons pu réduire les frais de transaction de 60%. Créer un NFT coûte maintenant moins de 1 TND.', '2025-11-19 10:30:00', 'announcement', 3),
(8, 'Lancement de la téléconsultation', 'La fonctionnalité de téléconsultation est maintenant disponible ! Les patients peuvent consulter leur médecin directement depuis l\'application via vidéo.', '2025-11-14 08:00:00', 'milestone', 4),
(9, 'Déploiement dans 10 fermes pilotes', 'Notre solution AgriTech a été déployée dans 10 fermes pilotes à travers la Tunisie. Les premiers résultats montrent une économie d\'eau de 45% et une augmentation des rendements de 25%.', '2025-11-16 15:00:00', 'milestone', 5),
(10, 'Nouvelle fonctionnalité : Prévisions météo', 'Nous avons intégré un système de prévisions météo avancé qui permet aux agriculteurs d\'anticiper les conditions climatiques et d\'optimiser leurs interventions.', '2025-11-19 12:00:00', 'update', 5),
(11, 'Nouvelle fonctionnalité : Prévisions météo 2', 'azertyuiop-azertyuiop-azertyuiop-azertyuiop-azertyuiop-azertyuiop-azertyuiop-azertyuiop-', '2025-11-27 10:22:09', 'announcement', 1);

-- --------------------------------------------------------

--
-- Table structure for table `categorie`
--

CREATE TABLE `categorie` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categorie`
--

INSERT INTO `categorie` (`id`, `nom`, `icon`, `description`) VALUES
(1, 'AI', 'bi-robot', 'Intelligence Artificielle et Machine Learning'),
(2, 'IoT', 'bi-cpu', 'Internet des Objets et Robotique'),
(3, 'Blockchain', 'bi-diagram-3', 'Technologies Blockchain et Cryptomonnaies'),
(4, 'Web', 'bi-code-slash', 'Développement Web et Mobile'),
(5, 'Data', 'bi-database', 'Data Science et Big Data'),
(6, 'Security', 'bi-shield-check', 'Cybersécurité et Protection des Données');

-- --------------------------------------------------------

--
-- Table structure for table `evenement`
--

CREATE TABLE `evenement` (
  `id` int(11) NOT NULL,
  `titre` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `lieu` varchar(200) DEFAULT NULL,
  `capacite` int(11) DEFAULT NULL,
  `type` enum('en_ligne','presentiel') DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inscription`
--

CREATE TABLE `inscription` (
  `id` int(11) NOT NULL,
  `statut` enum('valide','en_attente','refuse') DEFAULT NULL,
  `date_inscription` datetime DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `evenement_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `investissement`
--

CREATE TABLE `investissement` (
  `id` int(11) NOT NULL,
  `montant` decimal(10,2) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `message` text DEFAULT NULL,
  `statut` enum('en_attente','valide','refuse') DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `projet_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `code` varchar(6) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_used` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `code`, `created_at`, `expires_at`, `is_used`) VALUES
(19, 'chaabiomar288@gmail.com', '109409', '2025-12-03 15:08:48', '2025-12-03 15:38:48', 1),
(20, 'mohamedchaouachi69@gmail.com', '679218', '2025-12-03 15:26:50', '2025-12-03 15:56:50', 1),
(22, 'awissem349@gmail.com', '478443', '2025-12-03 16:59:24', '2025-12-03 17:29:24', 1),
(23, 'mohamedchaouachi69@gmail.com', '053060', '2025-12-03 17:01:39', '2025-12-03 17:31:39', 1),
(24, 'ons.trabelssi@esprit.tn', '669703', '2025-12-04 08:35:54', '2025-12-04 09:05:54', 0),
(26, 'awissem349@gmail.com', '319434', '2025-12-04 09:10:56', '2025-12-04 09:40:56', 1);

-- --------------------------------------------------------

--
-- Table structure for table `profil`
--

CREATE TABLE `profil` (
  `id` int(11) NOT NULL,
  `bio` text DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `competences` text DEFAULT NULL,
  `liens_sociaux` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`liens_sociaux`)),
  `domaine_expertise` varchar(100) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projet`
--

CREATE TABLE `projet` (
  `id` int(11) NOT NULL,
  `titre` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `budget_requis` decimal(10,2) DEFAULT NULL,
  `budget_actuel` decimal(10,2) DEFAULT NULL,
  `statut` enum('idee','prototype','mvp','production') DEFAULT 'idee',
  `date_creation` datetime DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projet`
--

INSERT INTO `projet` (`id`, `titre`, `description`, `budget_requis`, `budget_actuel`, `statut`, `date_creation`, `user_id`) VALUES
(1, 'Assistant IA Intelligent pour PME', 'Assistant virtuel basé sur l\'IA pour automatiser les tâches quotidiennes des petites et moyennes entreprises. Utilise le traitement du langage naturel pour comprendre et répondre aux demandes, gérer les emails, planifier les réunions et analyser les données.', 80000.00, 42500.00, 'prototype', '2025-11-19 20:44:50', 1),
(2, 'Maison Connectée Écologique', 'Système domotique intelligent pour optimiser la consommation énergétique et réduire l\'empreinte carbone. Intègre des capteurs IoT, l\'apprentissage automatique et une interface mobile intuitive.', 60000.00, 65000.00, 'mvp', '2025-11-19 20:44:50', 1),
(3, 'Plateforme NFT pour Artistes', 'Marketplace décentralisée permettant aux artistes tunisiens de créer et vendre leurs NFTs facilement. Basée sur la blockchain Ethereum avec des frais réduits.', 75000.00, 21000.00, 'idee', '2025-11-19 20:44:50', 1),
(4, 'Application Mobile de Santé Connectée', 'App mobile pour le suivi médical et la prise de rendez-vous avec téléconsultation intégrée. Permet aux patients de gérer leur santé et de communiquer avec leurs médecins.', 60000.00, 9000.00, 'prototype', '2025-11-19 20:44:50', 1),
(5, 'Solution AgriTech pour Agriculture Durable', 'Capteurs IoT et IA pour optimiser l\'irrigation et surveiller la santé des cultures en temps réel. Aide les agriculteurs à réduire la consommation d\'eau et augmenter les rendements.', 90000.00, 64800.00, 'mvp', '2025-11-19 20:44:50', 1);

-- --------------------------------------------------------

--
-- Table structure for table `projet_categorie`
--

CREATE TABLE `projet_categorie` (
  `id` int(11) NOT NULL,
  `projet_id` int(11) DEFAULT NULL,
  `categorie_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projet_categorie`
--

INSERT INTO `projet_categorie` (`id`, `projet_id`, `categorie_id`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 3),
(4, 4, 4),
(5, 5, 2);

-- --------------------------------------------------------

--
-- Table structure for table `reclamation`
--

CREATE TABLE `reclamation` (
  `id` int(11) NOT NULL,
  `objet` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `type` enum('technique','contenu','bug','suggestion','autre') DEFAULT NULL,
  `priorite` enum('basse','normale','haute','urgente') DEFAULT NULL,
  `statut` enum('en_attente','en_cours','resolue','fermee') DEFAULT NULL,
  `date_creation` datetime DEFAULT NULL,
  `date_resolution` datetime DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `remember_tokens`
--

CREATE TABLE `remember_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `remember_tokens`
--

INSERT INTO `remember_tokens` (`id`, `user_id`, `token`, `expires_at`, `created_at`) VALUES
(23, 9, 'f84854ad5766f936ff206fc22cf26678:$2y$10$8lqqD9uGrmaaA8SXW.G/H.krJdryXVBHuDq9y7dLY82WP89m/Qtgm', '2026-01-09 17:47:41', '2025-12-10 17:47:41');

-- --------------------------------------------------------

--
-- Table structure for table `reponse`
--

CREATE TABLE `reponse` (
  `id` int(11) NOT NULL,
  `contenu` text DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `sujet_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reponse_reclamation`
--

CREATE TABLE `reponse_reclamation` (
  `id` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `date_reponse` datetime DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `reclamation_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sujet`
--

CREATE TABLE `sujet` (
  `id` int(11) NOT NULL,
  `titre` varchar(200) DEFAULT NULL,
  `contenu` text DEFAULT NULL,
  `date_creation` datetime DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `categorie_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `id` int(11) NOT NULL,
  `montant` decimal(10,2) DEFAULT NULL,
  `type` enum('debit','credit') DEFAULT NULL,
  `statut` enum('pending','completed','failed') DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `investissement_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `mdp` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `banned_until` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nom`, `prenom`, `email`, `telephone`, `mdp`, `role`, `created_at`, `banned_until`) VALUES
(9, 'ayari', 'wissem', 'wissem@gmail.com', '95893212', 'wissem', 'admin', '2025-11-17 20:38:56', NULL),
(13, 'aouina', 'malek', 'malek@gmail.com', '50282358', 'Malek12@', 'user', '2025-11-18 10:39:21', NULL),
(17, 'ayari', 'wissem', 'awissem349@gmail.com', '+21695893212', 'Wissem@12', 'user', '2025-12-03 04:06:24', NULL),
(18, 'ch', 'mohamed', 'mohamedchaouachi69@gmail.com', '12345678', 'Mohamed@1', 'user', '2025-12-03 15:26:18', NULL),
(19, 'trabelssi', 'ons', 'ons.trabelssi@esprit.tn', '00000000', 'Ons@123aa', 'user', '2025-12-04 08:35:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `role` enum('visiteur','user','innovateur','Investisseur','Administrateur') DEFAULT NULL,
  `date_inscription` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `nom`, `prenom`, `email`, `password`, `telephone`, `role`, `date_inscription`) VALUES
(1, 'Test', 'User', 'test@kernel.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+216 12 345 678', 'innovateur', '2025-11-19 20:44:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `actualite`
--
ALTER TABLE `actualite`
  ADD PRIMARY KEY (`id`),
  ADD KEY `projet_id` (`projet_id`);

--
-- Indexes for table `categorie`
--
ALTER TABLE `categorie`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `evenement`
--
ALTER TABLE `evenement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `inscription`
--
ALTER TABLE `inscription`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `evenement_id` (`evenement_id`);

--
-- Indexes for table `investissement`
--
ALTER TABLE `investissement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `projet_id` (`projet_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_code` (`code`);

--
-- Indexes for table `profil`
--
ALTER TABLE `profil`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `projet`
--
ALTER TABLE `projet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `projet_categorie`
--
ALTER TABLE `projet_categorie`
  ADD PRIMARY KEY (`id`),
  ADD KEY `projet_id` (`projet_id`),
  ADD KEY `categorie_id` (`categorie_id`);

--
-- Indexes for table `reclamation`
--
ALTER TABLE `reclamation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_expires` (`expires_at`);

--
-- Indexes for table `reponse`
--
ALTER TABLE `reponse`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `sujet_id` (`sujet_id`);

--
-- Indexes for table `reponse_reclamation`
--
ALTER TABLE `reponse_reclamation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `reclamation_id` (`reclamation_id`);

--
-- Indexes for table `sujet`
--
ALTER TABLE `sujet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `categorie_id` (`categorie_id`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`id`),
  ADD KEY `investissement_id` (`investissement_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_interests`
--
ALTER TABLE `user_interests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_category` (`user_id`,`categorie_id`),
  ADD KEY `idx_user_interests` (`user_id`,`is_active`),
  ADD KEY `idx_category_interests` (`categorie_id`,`is_active`);

--
-- Indexes for table `project_followers`
--
ALTER TABLE `project_followers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_project` (`user_id`,`projet_id`),
  ADD KEY `idx_project_followers` (`projet_id`,`is_active`),
  ADD KEY `idx_user_follows` (`user_id`,`is_active`);

--
-- Indexes for table `user_notification_preferences`
--
ALTER TABLE `user_notification_preferences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_prefs` (`user_id`);

--
-- Indexes for table `notification_log`
--
ALTER TABLE `notification_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_notifications` (`user_id`,`created_at`),
  ADD KEY `idx_project_notifications` (`projet_id`,`created_at`),
  ADD KEY `idx_status_notifications` (`status`,`created_at`),
  ADD KEY `idx_channel_notifications` (`channel`,`status`);

--
-- Indexes for table `user_engagement_tracking`
--
ALTER TABLE `user_engagement_tracking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_engagement` (`user_id`,`created_at`),
  ADD KEY `idx_project_engagement` (`projet_id`,`created_at`),
  ADD KEY `idx_category_engagement` (`categorie_id`,`created_at`);

--
-- SYSTÈME DE NOTIFICATION INTELLIGENT - NOUVELLES TABLES
--

--
-- Table structure for table `user_interests`
--

CREATE TABLE `user_interests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `categorie_id` int(11) NOT NULL,
  `interest_level` enum('low','medium','high') DEFAULT 'medium',
  `date_added` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_interests`
--

INSERT INTO `user_interests` (`id`, `user_id`, `categorie_id`, `interest_level`, `date_added`, `is_active`) VALUES
(1, 18, 1, 'high', '2025-12-10 21:00:00', 1),
(2, 18, 5, 'medium', '2025-12-10 21:00:00', 1),
(3, 18, 6, 'medium', '2025-12-10 21:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `project_followers`
--

CREATE TABLE `project_followers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `projet_id` int(11) NOT NULL,
  `follow_type` enum('owner','investor','interested','collaborator') DEFAULT 'interested',
  `notification_frequency` enum('instant','daily','weekly','never') DEFAULT 'instant',
  `notification_types` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`notification_types`)),
  `date_followed` datetime DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_notification_preferences`
--

CREATE TABLE `user_notification_preferences` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email_notifications` tinyint(1) DEFAULT 1,
  `push_notifications` tinyint(1) DEFAULT 1,
  `sms_notifications` tinyint(1) DEFAULT 0,
  `digest_frequency` enum('never','daily','weekly','monthly') DEFAULT 'weekly',
  `quiet_hours_start` time DEFAULT '22:00:00',
  `quiet_hours_end` time DEFAULT '08:00:00',
  `timezone` varchar(50) DEFAULT 'Africa/Tunis',
  `language` enum('fr','ar','en') DEFAULT 'fr',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_notification_preferences`
--

INSERT INTO `user_notification_preferences` (`id`, `user_id`, `email_notifications`, `digest_frequency`, `language`) VALUES
(1, 18, 1, 'weekly', 'fr');

-- --------------------------------------------------------

--
-- Table structure for table `notification_log`
--

CREATE TABLE `notification_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `projet_id` int(11) DEFAULT NULL,
  `actualite_id` int(11) DEFAULT NULL,
  `notification_type` enum('actualite','digest','reminder','system') DEFAULT 'actualite',
  `channel` enum('email','push','sms','in_app') DEFAULT 'email',
  `subject` varchar(500) NOT NULL,
  `content` text NOT NULL,
  `recipient_email` varchar(255) NOT NULL,
  `status` enum('pending','sent','delivered','failed','bounced') DEFAULT 'pending',
  `sent_at` datetime DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL,
  `opened_at` datetime DEFAULT NULL,
  `clicked_at` datetime DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_engagement_tracking`
--

CREATE TABLE `user_engagement_tracking` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `projet_id` int(11) DEFAULT NULL,
  `categorie_id` int(11) DEFAULT NULL,
  `action_type` enum('view','like','share','comment','invest','follow','unfollow') NOT NULL,
  `engagement_score` decimal(3,2) DEFAULT 0.00,
  `session_duration` int(11) DEFAULT NULL,
  `device_type` enum('desktop','mobile','tablet') DEFAULT 'desktop',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- View structure for view `user_smart_recommendations`
--

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `user_smart_recommendations`  AS SELECT `u`.`id` AS `user_id`,`u`.`email` AS `email`,`u`.`nom` AS `nom`,`u`.`prenom` AS `prenom`,`p`.`id` AS `projet_id`,`p`.`titre` AS `projet_titre`,`c`.`nom` AS `categorie_nom`,`c`.`id` AS `categorie_id`,coalesce(`ui`.`interest_level`,'low') AS `interest_level`,coalesce(avg(`uet`.`engagement_score`),0) AS `avg_engagement_score`,count(`uet`.`id`) AS `interaction_count`,case when `pf`.`user_id` is not null then 'following' when `ui`.`user_id` is not null then 'interested' when avg(`uet`.`engagement_score`) > 0.7 then 'highly_engaged' when avg(`uet`.`engagement_score`) > 0.4 then 'moderately_engaged' else 'not_engaged' end AS `recommendation_level` FROM (((((`users` `u` join `projet` `p`) join `projet_categorie` `pc` on(`p`.`id` = `pc`.`projet_id`)) join `categorie` `c` on(`pc`.`categorie_id` = `c`.`id`)) left join `user_interests` `ui` on(`u`.`id` = `ui`.`user_id` and `c`.`id` = `ui`.`categorie_id` and `ui`.`is_active` = 1)) left join `project_followers` `pf` on(`u`.`id` = `pf`.`user_id` and `p`.`id` = `pf`.`projet_id` and `pf`.`is_active` = 1)) left join `user_engagement_tracking` `uet` on(`u`.`id` = `uet`.`user_id` and (`p`.`id` = `uet`.`projet_id` or `c`.`id` = `uet`.`categorie_id`)) WHERE `u`.`email` is not null and `u`.`email` <> '' GROUP BY `u`.`id`,`p`.`id`,`c`.`id` HAVING `recommendation_level` in ('following','interested','highly_engaged','moderately_engaged') ;

-- --------------------------------------------------------

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `actualite`
--
ALTER TABLE `actualite`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `categorie`
--
ALTER TABLE `categorie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `projet`
--
ALTER TABLE `projet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `projet_categorie`
--
ALTER TABLE `projet_categorie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_interests`
--
ALTER TABLE `user_interests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `project_followers`
--
ALTER TABLE `project_followers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_notification_preferences`
--
ALTER TABLE `user_notification_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notification_log`
--
ALTER TABLE `notification_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_engagement_tracking`
--
ALTER TABLE `user_engagement_tracking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `actualite`
--
ALTER TABLE `actualite`
  ADD CONSTRAINT `actualite_ibfk_1` FOREIGN KEY (`projet_id`) REFERENCES `projet` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `evenement`
--
ALTER TABLE `evenement`
  ADD CONSTRAINT `evenement_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateur` (`id`);

--
-- Constraints for table `inscription`
--
ALTER TABLE `inscription`
  ADD CONSTRAINT `inscription_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateur` (`id`),
  ADD CONSTRAINT `inscription_ibfk_2` FOREIGN KEY (`evenement_id`) REFERENCES `evenement` (`id`);

--
-- Constraints for table `investissement`
--
ALTER TABLE `investissement`
  ADD CONSTRAINT `investissement_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateur` (`id`),
  ADD CONSTRAINT `investissement_ibfk_2` FOREIGN KEY (`projet_id`) REFERENCES `projet` (`id`);

--
-- Constraints for table `profil`
--
ALTER TABLE `profil`
  ADD CONSTRAINT `profil_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateur` (`id`);

--
-- Constraints for table `projet`
--
ALTER TABLE `projet`
  ADD CONSTRAINT `projet_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateur` (`id`);

--
-- Constraints for table `projet_categorie`
--
ALTER TABLE `projet_categorie`
  ADD CONSTRAINT `projet_categorie_ibfk_1` FOREIGN KEY (`projet_id`) REFERENCES `projet` (`id`),
  ADD CONSTRAINT `projet_categorie_ibfk_2` FOREIGN KEY (`categorie_id`) REFERENCES `categorie` (`id`);

--
-- Constraints for table `reclamation`
--
ALTER TABLE `reclamation`
  ADD CONSTRAINT `reclamation_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateur` (`id`);

--
-- Constraints for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD CONSTRAINT `remember_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reponse`
--
ALTER TABLE `reponse`
  ADD CONSTRAINT `reponse_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateur` (`id`),
  ADD CONSTRAINT `reponse_ibfk_2` FOREIGN KEY (`sujet_id`) REFERENCES `sujet` (`id`);

--
-- Constraints for table `reponse_reclamation`
--
ALTER TABLE `reponse_reclamation`
  ADD CONSTRAINT `reponse_reclamation_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `utilisateur` (`id`),
  ADD CONSTRAINT `reponse_reclamation_ibfk_2` FOREIGN KEY (`reclamation_id`) REFERENCES `reclamation` (`id`);

--
-- Constraints for table `sujet`
--
ALTER TABLE `sujet`
  ADD CONSTRAINT `sujet_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateur` (`id`),
  ADD CONSTRAINT `sujet_ibfk_2` FOREIGN KEY (`categorie_id`) REFERENCES `categorie` (`id`);

--
-- Constraints for table `transaction`
--
ALTER TABLE `transaction`
  ADD CONSTRAINT `transaction_ibfk_1` FOREIGN KEY (`investissement_id`) REFERENCES `investissement` (`id`);

--
-- Constraints for table `user_interests`
--
ALTER TABLE `user_interests`
  ADD CONSTRAINT `user_interests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_interests_ibfk_2` FOREIGN KEY (`categorie_id`) REFERENCES `categorie` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_followers`
--
ALTER TABLE `project_followers`
  ADD CONSTRAINT `project_followers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_followers_ibfk_2` FOREIGN KEY (`projet_id`) REFERENCES `projet` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_notification_preferences`
--
ALTER TABLE `user_notification_preferences`
  ADD CONSTRAINT `user_notification_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notification_log`
--
ALTER TABLE `notification_log`
  ADD CONSTRAINT `notification_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notification_log_ibfk_2` FOREIGN KEY (`projet_id`) REFERENCES `projet` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `notification_log_ibfk_3` FOREIGN KEY (`actualite_id`) REFERENCES `actualite` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_engagement_tracking`
--
ALTER TABLE `user_engagement_tracking`
  ADD CONSTRAINT `user_engagement_tracking_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_engagement_tracking_ibfk_2` FOREIGN KEY (`projet_id`) REFERENCES `projet` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `user_engagement_tracking_ibfk_3` FOREIGN KEY (`categorie_id`) REFERENCES `categorie` (`id`) ON DELETE SET NULL;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
