-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 19 nov. 2025 à 22:52
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
-- Base de données : `kernel1`
--

-- --------------------------------------------------------

--
-- Structure de la table `actualite`
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
-- Déchargement des données de la table `actualite`
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
(10, 'Nouvelle fonctionnalité : Prévisions météo', 'Nous avons intégré un système de prévisions météo avancé qui permet aux agriculteurs d\'anticiper les conditions climatiques et d\'optimiser leurs interventions.', '2025-11-19 12:00:00', 'update', 5);

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

CREATE TABLE `categorie` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categorie`
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
-- Structure de la table `evenement`
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
-- Structure de la table `inscription`
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
-- Structure de la table `investissement`
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
-- Structure de la table `profil`
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
-- Structure de la table `projet`
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
-- Déchargement des données de la table `projet`
--

INSERT INTO `projet` (`id`, `titre`, `description`, `budget_requis`, `budget_actuel`, `statut`, `date_creation`, `user_id`) VALUES
(1, 'Assistant IA Intelligent pour PME', 'Assistant virtuel basé sur l\'IA pour automatiser les tâches quotidiennes des petites et moyennes entreprises. Utilise le traitement du langage naturel pour comprendre et répondre aux demandes, gérer les emails, planifier les réunions et analyser les données.', 80000.00, 42500.00, 'prototype', '2025-11-19 20:44:50', 1),
(2, 'Maison Connectée Écologique', 'Système domotique intelligent pour optimiser la consommation énergétique et réduire l\'empreinte carbone. Intègre des capteurs IoT, l\'apprentissage automatique et une interface mobile intuitive.', 60000.00, 65000.00, 'mvp', '2025-11-19 20:44:50', 1),
(3, 'Plateforme NFT pour Artistes', 'Marketplace décentralisée permettant aux artistes tunisiens de créer et vendre leurs NFTs facilement. Basée sur la blockchain Ethereum avec des frais réduits.', 75000.00, 21000.00, 'idee', '2025-11-19 20:44:50', 1),
(4, 'Application Mobile de Santé Connectée', 'App mobile pour le suivi médical et la prise de rendez-vous avec téléconsultation intégrée. Permet aux patients de gérer leur santé et de communiquer avec leurs médecins.', 60000.00, 9000.00, 'prototype', '2025-11-19 20:44:50', 1),
(5, 'Solution AgriTech pour Agriculture Durable', 'Capteurs IoT et IA pour optimiser l\'irrigation et surveiller la santé des cultures en temps réel. Aide les agriculteurs à réduire la consommation d\'eau et augmenter les rendements.', 90000.00, 64800.00, 'mvp', '2025-11-19 20:44:50', 1);

-- --------------------------------------------------------

--
-- Structure de la table `projet_categorie`
--

CREATE TABLE `projet_categorie` (
  `id` int(11) NOT NULL,
  `projet_id` int(11) DEFAULT NULL,
  `categorie_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `projet_categorie`
--

INSERT INTO `projet_categorie` (`id`, `projet_id`, `categorie_id`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 3),
(4, 4, 4),
(5, 5, 2);

-- --------------------------------------------------------

--
-- Structure de la table `reclamation`
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
-- Structure de la table `reponse`
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
-- Structure de la table `reponse_reclamation`
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
-- Structure de la table `sujet`
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
-- Structure de la table `transaction`
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
-- Structure de la table `utilisateur`
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
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `nom`, `prenom`, `email`, `password`, `telephone`, `role`, `date_inscription`) VALUES
(1, 'Test', 'User', 'test@kernel.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+216 12 345 678', 'innovateur', '2025-11-19 20:44:50');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `actualite`
--
ALTER TABLE `actualite`
  ADD PRIMARY KEY (`id`),
  ADD KEY `projet_id` (`projet_id`);

--
-- Index pour la table `categorie`
--
ALTER TABLE `categorie`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `evenement`
--
ALTER TABLE `evenement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `inscription`
--
ALTER TABLE `inscription`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `evenement_id` (`evenement_id`);

--
-- Index pour la table `investissement`
--
ALTER TABLE `investissement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `projet_id` (`projet_id`);

--
-- Index pour la table `profil`
--
ALTER TABLE `profil`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `projet`
--
ALTER TABLE `projet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `projet_categorie`
--
ALTER TABLE `projet_categorie`
  ADD PRIMARY KEY (`id`),
  ADD KEY `projet_id` (`projet_id`),
  ADD KEY `categorie_id` (`categorie_id`);

--
-- Index pour la table `reclamation`
--
ALTER TABLE `reclamation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `reponse`
--
ALTER TABLE `reponse`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `sujet_id` (`sujet_id`);

--
-- Index pour la table `reponse_reclamation`
--
ALTER TABLE `reponse_reclamation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `reclamation_id` (`reclamation_id`);

--
-- Index pour la table `sujet`
--
ALTER TABLE `sujet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `categorie_id` (`categorie_id`);

--
-- Index pour la table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`id`),
  ADD KEY `investissement_id` (`investissement_id`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `actualite`
--
ALTER TABLE `actualite`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `categorie`
--
ALTER TABLE `categorie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `projet`
--
ALTER TABLE `projet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `projet_categorie`
--
ALTER TABLE `projet_categorie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `actualite`
--
ALTER TABLE `actualite`
  ADD CONSTRAINT `actualite_ibfk_1` FOREIGN KEY (`projet_id`) REFERENCES `projet` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `evenement`
--
ALTER TABLE `evenement`
  ADD CONSTRAINT `evenement_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateur` (`id`);

--
-- Contraintes pour la table `inscription`
--
ALTER TABLE `inscription`
  ADD CONSTRAINT `inscription_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateur` (`id`),
  ADD CONSTRAINT `inscription_ibfk_2` FOREIGN KEY (`evenement_id`) REFERENCES `evenement` (`id`);

--
-- Contraintes pour la table `investissement`
--
ALTER TABLE `investissement`
  ADD CONSTRAINT `investissement_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateur` (`id`),
  ADD CONSTRAINT `investissement_ibfk_2` FOREIGN KEY (`projet_id`) REFERENCES `projet` (`id`);

--
-- Contraintes pour la table `profil`
--
ALTER TABLE `profil`
  ADD CONSTRAINT `profil_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateur` (`id`);

--
-- Contraintes pour la table `projet`
--
ALTER TABLE `projet`
  ADD CONSTRAINT `projet_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateur` (`id`);

--
-- Contraintes pour la table `projet_categorie`
--
ALTER TABLE `projet_categorie`
  ADD CONSTRAINT `projet_categorie_ibfk_1` FOREIGN KEY (`projet_id`) REFERENCES `projet` (`id`),
  ADD CONSTRAINT `projet_categorie_ibfk_2` FOREIGN KEY (`categorie_id`) REFERENCES `categorie` (`id`);

--
-- Contraintes pour la table `reclamation`
--
ALTER TABLE `reclamation`
  ADD CONSTRAINT `reclamation_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateur` (`id`);

--
-- Contraintes pour la table `reponse`
--
ALTER TABLE `reponse`
  ADD CONSTRAINT `reponse_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateur` (`id`),
  ADD CONSTRAINT `reponse_ibfk_2` FOREIGN KEY (`sujet_id`) REFERENCES `sujet` (`id`);

--
-- Contraintes pour la table `reponse_reclamation`
--
ALTER TABLE `reponse_reclamation`
  ADD CONSTRAINT `reponse_reclamation_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `utilisateur` (`id`),
  ADD CONSTRAINT `reponse_reclamation_ibfk_2` FOREIGN KEY (`reclamation_id`) REFERENCES `reclamation` (`id`);

--
-- Contraintes pour la table `sujet`
--
ALTER TABLE `sujet`
  ADD CONSTRAINT `sujet_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilisateur` (`id`),
  ADD CONSTRAINT `sujet_ibfk_2` FOREIGN KEY (`categorie_id`) REFERENCES `categorie` (`id`);

--
-- Contraintes pour la table `transaction`
--
ALTER TABLE `transaction`
  ADD CONSTRAINT `transaction_ibfk_1` FOREIGN KEY (`investissement_id`) REFERENCES `investissement` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
