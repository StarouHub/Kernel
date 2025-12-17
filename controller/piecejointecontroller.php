<?php
require_once __DIR__ . '/../model/PieceJointe.php';

class PieceJointeController {
    private $db;
    private $uploadDir = __DIR__ . '/../../uploads/reclamations/';

    public function __construct() {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
        
        // Créer le dossier uploads s'il n'existe pas
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    public function upload($files, $reclamation_id, $reponse_id = null) {
        try {
            $uploadedFiles = [];
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt', 'mp4'];
            $maxSize = 10 * 1024 * 1024; // 10 Mo

            foreach ($files['tmp_name'] as $key => $tmpName) {
                if ($files['error'][$key] !== UPLOAD_ERR_OK) {
                    continue;
                }

                $originalName = $files['name'][$key];
                $fileSize = $files['size'][$key];
                $fileType = $files['type'][$key];
                $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

                // Validation
                if ($fileSize > $maxSize) {
                    throw new Exception("Le fichier $originalName dépasse la taille limite de 10 Mo");
                }

                if (!in_array($extension, $allowedExtensions)) {
                    throw new Exception("Le type de fichier $originalName n'est pas autorisé");
                }

                // Créer le dossier pour la réclamation
                $reclamationDir = $this->uploadDir . $reclamation_id . '/';
                if (!file_exists($reclamationDir)) {
                    mkdir($reclamationDir, 0777, true);
                }

                // Générer un nom unique
                $uniqueName = uniqid() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
                $destination = $reclamationDir . $uniqueName;

                // Déplacer le fichier
                if (move_uploaded_file($tmpName, $destination)) {
                    // Enregistrer en base
                    $sql = "INSERT INTO pieces_jointes 
                            (reclamation_id, reponse_id, nom_original, chemin, taille_octets, type_mime, date_upload) 
                            VALUES (?, ?, ?, ?, ?, ?, NOW())";
                    
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute([
                        $reclamation_id,
                        $reponse_id,
                        $originalName,
                        $destination,
                        $fileSize,
                        $fileType
                    ]);

                    $uploadedFiles[] = [
                        'id' => $this->db->lastInsertId(),
                        'nom_original' => $originalName,
                        'chemin' => $destination,
                        'taille' => $fileSize,
                        'type' => $fileType
                    ];
                }
            }

            return ['success' => true, 'files' => $uploadedFiles];

        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getByReclamation($reclamation_id) {
        $sql = "SELECT * FROM pieces_jointes 
                WHERE reclamation_id = ? 
                ORDER BY date_upload DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$reclamation_id]);
        return $stmt->fetchAll();
    }

    public function getByReponse($reponse_id) {
        $sql = "SELECT * FROM pieces_jointes 
                WHERE reponse_id = ? 
                ORDER BY date_upload DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$reponse_id]);
        return $stmt->fetchAll();
    }

    public function delete($id) {
        try {
            // Récupérer le fichier
            $sql = "SELECT chemin FROM pieces_jointes WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $file = $stmt->fetch();

            if ($file && file_exists($file['chemin'])) {
                unlink($file['chemin']);
            }

            // Supprimer de la base
            $sql = "DELETE FROM pieces_jointes WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);

            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
?>