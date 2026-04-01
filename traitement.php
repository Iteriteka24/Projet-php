<?php
// traitement.php
//session_start();
require_once 'config.php';

// Fonction de validation
function validateForm($data, $files) {
    $errors = [];
    
    // Validation du nom
    if (empty($data['nom'])) {
        $errors['nom'] = "Le nom est requis";
    } elseif (strlen($data['nom']) < 2 || strlen($data['nom']) > 100) {
        $errors['nom'] = "Le nom doit contenir entre 2 et 100 caractères";
    } elseif (!preg_match("/^[a-zA-ZÀ-ÿ\s-]+$/", $data['nom'])) {
        $errors['nom'] = "Le nom ne peut contenir que des lettres, espaces et tirets";
    }
    
    // Validation du prénom
    if (empty($data['prenom'])) {
        $errors['prenom'] = "Le prénom est requis";
    } elseif (strlen($data['prenom']) < 2 || strlen($data['prenom']) > 100) {
        $errors['prenom'] = "Le prénom doit contenir entre 2 et 100 caractères";
    }
    
    // Validation de la date de naissance
    if (empty($data['date_naissance'])) {
        $errors['date_naissance'] = "La date de naissance est requise";
    } else {
        $date = DateTime::createFromFormat('Y-m-d', $data['date_naissance']);
        if (!$date) {
            $errors['date_naissance'] = "Format de date invalide";
        } else {
            $today = new DateTime();
            $age = $today->diff($date)->y;
            if ($age < 18) {
                $errors['date_naissance'] = "Vous devez avoir au moins 18 ans";
            } elseif ($age > 120) {
                $errors['date_naissance'] = "Âge invalide";
            }
        }
    }
    
    // Validation du genre
    if (empty($data['genre'])) {
        $errors['genre'] = "Le genre est requis";
    } elseif (!in_array($data['genre'], ['homme', 'femme', 'autre'])) {
        $errors['genre'] = "Genre invalide";
    }
    
    // Validation de la situation familiale
    if (empty($data['situation_familiale'])) {
        $errors['situation_familiale'] = "La situation familiale est requise";
    } elseif (!in_array($data['situation_familiale'], ['celibataire', 'marie', 'divorce', 'veuf'])) {
        $errors['situation_familiale'] = "Situation familiale invalide";
    }
    
    // Validation de l'email
    if (empty($data['email'])) {
        $errors['email'] = "L'email est requis";
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Format d'email invalide";
    } elseif (strlen($data['email']) > 255) {
        $errors['email'] = "L'email est trop long";
    }
    
    // Validation du téléphone (optionnel)
    if (!empty($data['telephone'])) {
        $telephone = preg_replace('/[^0-9+]/', '', $data['telephone']);
        if (!preg_match('/^\+?[0-9]{10,15}$/', $telephone)) {
            $errors['telephone'] = "Format de téléphone invalide";
        }
    }
    
    // Validation de la photo
    if (empty($files['photo']['name'])) {
        $errors['photo'] = "La photo est requise";
    } else {
        $file = $files['photo'];
        $fileSize = $file['size'];
        $fileError = $file['error'];
        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if ($fileError !== UPLOAD_ERR_OK) {
            $errors['photo'] = "Erreur lors du téléchargement de la photo";
        } elseif ($fileSize > MAX_FILE_SIZE) {
            $errors['photo'] = "La photo ne doit pas dépasser 5 Mo";
        } elseif (!in_array($fileExt, ALLOWED_EXTENSIONS)) {
            $errors['photo'] = "Format de photo non autorisé (JPG, JPEG, PNG, GIF uniquement)";
        }
        
        // Vérification supplémentaire avec getimagesize
        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            $errors['photo'] = "Le fichier n'est pas une image valide";
        }
    }
    
    return $errors;
}

// Fonction pour uploader la photo
function uploadPhoto($file) {
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $destination = UPLOAD_DIR . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $filename;
    }
    
    return false;
}

// Traitement principal
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nettoyage des données
    $form_data = [
        'nom' => trim($_POST['nom'] ?? ''),
        'prenom' => trim($_POST['prenom'] ?? ''),
        'date_naissance' => $_POST['date_naissance'] ?? '',
        'genre' => $_POST['genre'] ?? '',
        'situation_familiale' => $_POST['situation_familiale'] ?? '',
        'email' => trim($_POST['email'] ?? ''),
        'telephone' => trim($_POST['telephone'] ?? ''),
        'aadresse' => trim($_POST['aadresse'] ?? '')
    ];
    
    // Validation
    $errors = validateForm($form_data, $_FILES);
    
    if (empty($errors)) {
        try {
            // Upload de la photo
            $photoFilename = uploadPhoto($_FILES['photo']);
            
            if ($photoFilename === false) {
                throw new Exception("Erreur lors de l'upload de la photo");
            }
            
            // Vérifier si l'email existe déjà
            $stmt = $pdo->prepare("SELECT id FROM personne WHERE email = ?");
            $stmt->execute([$form_data['email']]);
            
            if ($stmt->fetch()) {
                $errors['email'] = "Cet email est déjà utilisé";
                // Supprimer la photo uploadée si l'email existe déjà
                unlink(UPLOAD_DIR . $photoFilename);
            } else {
                // Insertion dans la base de données
                $sql = "INSERT INTO personne (nom, prenom, date_naissance, genre, situation_familiale, 
                        email, telephone, aadresse, photo) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $form_data['nom'],
                    $form_data['prenom'],
                    $form_data['date_naissance'],
                    $form_data['genre'],
                    $form_data['situation_familiale'],
                    $form_data['email'],
                    $form_data['telephone'],
                    $form_data['aadresse'],
                    $photoFilename
                ]);
                
                $_SESSION['success'] = "Les informations ont été enregistrées avec succès !";
                
                // Redirection pour éviter la re-soumission du formulaire
                header("Location: formulaire.php");
                exit;
            }
        } catch (Exception $e) {
            $errors['general'] = "Erreur lors de l'enregistrement : " . $e->getMessage();
        }
    }
    
    // En cas d'erreurs, on les stocke pour les afficher dans le formulaire
    if (!empty($errors)) {
        // On garde les données saisies
        $_SESSION['form_data'] = $form_data;
        $_SESSION['errors'] = $errors;
    }
}
?>
