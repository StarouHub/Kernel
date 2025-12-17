-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 11, 2025 at 02:48 AM
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
-- Table structure for table `taches_projet`
--

CREATE TABLE `taches_projet` (
  `id` int(11) NOT NULL,
  `projet_id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `statut` enum('a_faire','en_cours','termine') DEFAULT 'a_faire',
  `priorite` enum('basse','moyenne','haute') DEFAULT 'moyenne',
  `date_creation` datetime DEFAULT current_timestamp(),
  `date_debut` datetime DEFAULT NULL,
  `date_fin` datetime DEFAULT NULL,
  `date_echeance` datetime DEFAULT NULL,
  `assignee_id` int(11) DEFAULT NULL,
  `ordre` int(11) DEFAULT 0,
  `couleur` varchar(7) DEFAULT '#3B82F6',
  `tags` text DEFAULT NULL,
  `temps_estime` int(11) DEFAULT NULL COMMENT 'Temps estimé en heures',
  `temps_passe` int(11) DEFAULT 0 COMMENT 'Temps passé en heures',
  `created_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `taches_projet`
--

INSERT INTO `taches_projet` (`id`, `projet_id`, `titre`, `description`, `statut`, `priorite`, `date_creation`, `date_debut`, `date_fin`, `date_echeance`, `assignee_id`, `ordre`, `couleur`, `tags`, `temps_estime`, `temps_passe`, `created_by`, `updated_at`) VALUES
(16, 1, 'Implémenter API OpenAI', 'Intégrer l\'API OpenAI pour le traitement du langage naturel', 'a_faire', 'haute', '2025-12-11 01:36:09', NULL, NULL, NULL, NULL, 1, '#EF4444', 'API,IA,Backend', 16, 0, 1, '2025-12-11 02:35:07'),
(17, 1, 'Tests de charge serveur', 'Effectuer des tests de performance et de charge', 'a_faire', 'moyenne', '2025-12-11 01:36:09', NULL, NULL, NULL, NULL, 2, '#F59E0B', 'Tests,Performance', 8, 0, 1, '2025-12-11 01:36:09'),
(18, 1, 'Documentation technique', 'Rédiger la documentation complète de l\'API', 'a_faire', 'basse', '2025-12-11 01:36:09', NULL, NULL, NULL, NULL, 3, '#10B981', 'Documentation', 12, 0, 1, '2025-12-11 01:36:09'),
(19, 1, 'Développement interface admin', 'Créer l\'interface d\'administration', 'en_cours', 'haute', '2025-12-11 01:36:09', NULL, NULL, NULL, NULL, 4, '#3B82F6', 'Frontend,Admin', 20, 0, 1, '2025-12-11 01:36:09'),
(20, 1, 'Intégration base de données', 'Optimiser les requêtes et la structure BDD', 'en_cours', 'moyenne', '2025-12-11 01:36:09', NULL, NULL, NULL, NULL, 5, '#8B5CF6', 'Database,Backend', 10, 0, 1, '2025-12-11 01:36:09'),
(21, 1, 'Design interface utilisateur', 'Finaliser le design UX/UI', 'termine', 'haute', '2025-12-06 01:36:09', NULL, NULL, NULL, NULL, 6, '#10B981', 'Design,UX', 24, 0, 1, '2025-12-11 01:36:09'),
(22, 1, 'Prototype v1.0', 'Développer le premier prototype fonctionnel', 'termine', 'haute', '2025-12-01 01:36:09', NULL, NULL, NULL, NULL, 7, '#10B981', 'Prototype,MVP', 40, 0, 1, '2025-12-11 01:36:09'),
(23, 1, 'Tests utilisateurs', 'Effectuer les tests avec les utilisateurs finaux', 'termine', 'moyenne', '2025-12-08 01:36:09', NULL, NULL, NULL, NULL, 8, '#10B981', 'Tests,UX', 16, 0, 1, '2025-12-11 01:36:09'),
(31, 2, 'Installation capteurs IoT', 'Installer et configurer les capteurs dans la maison témoin', 'en_cours', 'haute', '2025-12-11 01:36:09', NULL, NULL, NULL, NULL, 1, '#EF4444', 'IoT,Hardware', 12, 0, 1, '2025-12-11 01:36:09'),
(32, 2, 'Développement app mobile', 'Créer l\'application mobile de contrôle', 'a_faire', 'haute', '2025-12-11 01:36:09', NULL, NULL, NULL, NULL, 2, '#3B82F6', 'Mobile,Frontend', 30, 0, 1, '2025-12-11 01:36:09'),
(33, 2, 'Algorithme d\'optimisation', 'Développer l\'IA d\'optimisation énergétique', 'en_cours', 'haute', '2025-12-11 01:36:09', NULL, NULL, NULL, NULL, 3, '#8B5CF6', 'IA,Algorithme', 25, 0, 1, '2025-12-11 01:36:09'),
(34, 2, 'Tests de consommation', 'Mesurer l\'efficacité énergétique', 'termine', 'moyenne', '2025-12-04 01:36:09', NULL, NULL, NULL, NULL, 4, '#10B981', 'Tests,Energie', 8, 0, 1, '2025-12-11 01:36:09'),
(38, 3, 'Smart contracts Ethereum', 'Développer les contrats intelligents', 'a_faire', 'haute', '2025-12-11 01:36:09', NULL, NULL, NULL, NULL, 1, '#EF4444', 'Blockchain,Smart Contracts', 20, 0, 1, '2025-12-11 01:36:09'),
(40, 3, 'Système de paiement', 'Intégrer les paiements en crypto', 'en_cours', 'haute', '2025-12-11 01:36:09', NULL, NULL, NULL, NULL, 3, '#3B82F6', 'Payment,Crypto', 18, 0, 1, '2025-12-11 01:36:09');

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
(1, 'Admin', 'System', 'admin@kernel.tn', '00000000', 'admin123', 'admin', '2025-12-11 00:36:09', NULL),
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
-- Indexes for table `taches_projet`
--
ALTER TABLE `taches_projet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `projet_id` (`projet_id`),
  ADD KEY `assignee_id` (`assignee_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `statut` (`statut`),
  ADD KEY `priorite` (`priorite`),
  ADD KEY `idx_taches_projet_status_priority` (`statut`,`priorite`),
  ADD KEY `idx_taches_projet_dates` (`date_creation`,`date_echeance`),
  ADD KEY `idx_taches_projet_ordre` (`projet_id`,`ordre`);

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `taches_projet`
--
ALTER TABLE `taches_projet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `taches_projet`
--
ALTER TABLE `taches_projet`
  ADD CONSTRAINT `taches_projet_ibfk_1` FOREIGN KEY (`projet_id`) REFERENCES `projet` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `taches_projet_ibfk_2` FOREIGN KEY (`assignee_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `taches_projet_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/* for wissem*/;
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 10 déc. 2025 à 18:11
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `kernel`
--

-- --------------------------------------------------------

--
-- Structure de la table `password_resets`
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
-- Déchargement des données de la table `password_resets`
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
-- Structure de la table `remember_tokens`
--

CREATE TABLE `remember_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `remember_tokens`
--

INSERT INTO `remember_tokens` (`id`, `user_id`, `token`, `expires_at`, `created_at`) VALUES
(23, 9, 'f84854ad5766f936ff206fc22cf26678:$2y$10$8lqqD9uGrmaaA8SXW.G/H.krJdryXVBHuDq9y7dLY82WP89m/Qtgm', '2026-01-09 17:47:41', '2025-12-10 17:47:41');

-- --------------------------------------------------------

--
-- Structure de la table `users`
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
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `nom`, `prenom`, `email`, `telephone`, `mdp`, `role`, `created_at`, `banned_until`) VALUES
(9, 'ayari', 'wissem', 'wissem@gmail.com', '95893212', 'wissem', 'admin', '2025-11-17 20:38:56', NULL),
(13, 'aouina', 'malek', 'malek@gmail.com', '50282358', 'Malek12@', 'user', '2025-11-18 10:39:21', NULL),
(17, 'ayari', 'wissem', 'awissem349@gmail.com', '+21695893212', 'Wissem@12', 'user', '2025-12-03 04:06:24', NULL),
(18, 'ch', 'mohamed', 'mohamedchaouachi69@gmail.com', '12345678', 'Mohamed@1', 'user', '2025-12-03 15:26:18', NULL),
(19, 'trabelssi', 'ons', 'ons.trabelssi@esprit.tn', '00000000', 'Ons@123aa', 'user', '2025-12-04 08:35:29', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_code` (`code`);

--
-- Index pour la table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_expires` (`expires_at`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT pour la table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD CONSTRAINT `remember_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*end wissem*/;