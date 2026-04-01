<?php
// formulaire.php
session_start();
require_once 'config.php';

// Initialiser les variables
$errors = [];
$form_data = [
    'nom' => '',
    'prenom' => '',
    'date_naissance' => '',
    'genre' => '',
    'situation_familiale' => '',
    'email' => '',
    'telephone' => '',
    'aadresse' => ''
];

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'traitement.php';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'identité</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 2em;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="date"],
        textarea,
        select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e1e1;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #667eea;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .radio-group {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .radio-option {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .radio-option input[type="radio"] {
            width: auto;
        }

        .file-input {
            border: 2px dashed #e1e1e1;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            cursor: pointer;
            transition: border-color 0.3s;
        }

        .file-input:hover {
            border-color: #667eea;
        }

        .file-input input[type="file"] {
            display: none;
        }

        .file-input label {
            cursor: pointer;
            color: #667eea;
            font-weight: 600;
        }

        .preview-image {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            display: none;
            border-radius: 8px;
            border: 2px solid #e1e1e1;
        }

        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: transform 0.2s;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
        }

        .error-message {
            background: #fee;
            color: #c33;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #c33;
        }

        .success-message {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #2e7d32;
        }

        .error-field {
            border-color: #c33 !important;
        }

        .error-text {
            color: #c33;
            font-size: 14px;
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            
            .radio-group {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Formulaire d'identité</h1>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="success-message">
                <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <strong>Veuillez corriger les erreurs suivantes :</strong>
                <ul style="margin-top: 10px; margin-left: 20px;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data" id="identityForm">
            <div class="form-group">
                <label for="nom">Nom *</label>
                <input type="text" id="nom" name="nom" 
                       value="<?php echo htmlspecialchars($form_data['nom']); ?>" 
                       class="<?php echo isset($errors['nom']) ? 'error-field' : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="prenom">Prénom *</label>
                <input type="text" id="prenom" name="prenom" 
                       value="<?php echo htmlspecialchars($form_data['prenom']); ?>" 
                       class="<?php echo isset($errors['prenom']) ? 'error-field' : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="date_naissance">Date de naissance *</label>
                <input type="date" id="date_naissance" name="date_naissance" 
                       value="<?php echo htmlspecialchars($form_data['date_naissance']); ?>" 
                       class="<?php echo isset($errors['date_naissance']) ? 'error-field' : ''; ?>" 
                       max="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="form-group">
                <label>Genre *</label>
                <div class="radio-group">
                    <div class="radio-option">
                        <input type="radio" id="homme" name="genre" value="homme" 
                               <?php echo ($form_data['genre'] === 'homme') ? 'checked' : ''; ?> required>
                        <label for="homme">Homme</label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" id="femme" name="genre" value="femme" 
                               <?php echo ($form_data['genre'] === 'femme') ? 'checked' : ''; ?>>
                        <label for="femme">Femme</label>
                    </div>
                  <!--  <div class="radio-option">
                        <input type="radio" id="autre" name="genre" value="autre" 
                               <?php echo ($form_data['genre'] === 'autre') ? 'checked' : ''; ?>>
                        <label for="autre">Autre</label>
                    </div>-->
                </div>
            </div>

            <div class="form-group">
                <label for="situation_familiale">Situation familiale *</label>
                <select id="situation_familiale" name="situation_familiale" required>
                    <option value="">Sélectionnez...</option>
                    <option value="celibataire" <?php echo ($form_data['situation_familiale'] === 'celibataire') ? 'selected' : ''; ?>>Célibataire</option>
                    <option value="marie" <?php echo ($form_data['situation_familiale'] === 'marie') ? 'selected' : ''; ?>>Marié(e)</option>
                    <option value="divorce" <?php echo ($form_data['situation_familiale'] === 'divorce') ? 'selected' : ''; ?>>Divorcé(e)</option>
                    <option value="veuf" <?php echo ($form_data['situation_familiale'] === 'veuf') ? 'selected' : ''; ?>>Veuf/Veuve</option>
                </select>
            </div>

            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" 
                       value="<?php echo htmlspecialchars($form_data['email']); ?>" 
                       class="<?php echo isset($errors['email']) ? 'error-field' : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="telephone">Téléphone</label>
                <input type="tel" id="telephone" name="telephone" 
                       value="<?php echo htmlspecialchars($form_data['telephone']); ?>" 
                       pattern="[0-9+\-\s]+" 
                       placeholder="Ex: +33 6 12 34 56 78">
            </div>

            <div class="form-group">
                <label for="adresse">Adresse</label>
                <textarea id="adresse" name="adresse"><?php echo htmlspecialchars($form_data['aadresse']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="photo">Photo *</label>
                <div class="file-input" onclick="document.getElementById('photo').click()">
                    <input type="file" id="photo" name="photo" accept="image/*" 
                           onchange="previewImage(this)" required>
                    <label for="photo">Cliquez pour sélectionner une photo</label>
                    <p style="color: #999; margin-top: 10px; font-size: 14px;">
                        Formats acceptés : JPG, JPEG, PNG, GIF (Max 5 Mo)
                    </p>
                </div>
                <img id="preview" class="preview-image" src="#" alt="Aperçu">
            </div>

            <button type="submit" class="btn-submit">Enregistrer</button>
        </form>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('preview');
            const file = input.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        }

        // Validation côté client
        document.getElementById('identityForm').addEventListener('submit', function(e) {
            const photo = document.getElementById('photo');
            const maxSize = <?php echo MAX_FILE_SIZE; ?>;
            
            if (photo.files.length > 0) {
                if (photo.files[0].size > maxSize) {
                    e.preventDefault();
                    alert('La photo ne doit pas dépasser 5 Mo');
                }
            }
        });
    </script>
</body>
</html>
