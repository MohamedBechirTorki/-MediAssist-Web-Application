<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
} 

$mysqli = new mysqli("localhost", "root", "", "mediassistdb");
if ($mysqli->connect_error) {
    die("Erreur de connexion : " . $mysqli->connect_error);
}

$user_id = $_SESSION['user_id'];

// Récupération des médicaments pour cet utilisateur
$stmt = $mysqli->prepare("SELECT * FROM medicaments WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$medicaments = [];
while ($row = $result->fetch_assoc()) {
    $medicaments[] = $row;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord - MediAssist</title>
    <script src="https://kit.fontawesome.com/your_kit_code.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="assets/css/style.css">
    <link id="darkMode" rel="stylesheet" href="assets/css/dark-style.css">

</head>
<body class="container dash-body">
    <nav class="dash-nav">
        <div class="active"><span><i class="fa-solid fa-capsules"></i></span><p class="dash-p">Médicaments</p></div>
        <div><span><i class="fa-regular fa-calendar-check"></i></span> <p class="dash-p">Rendez-vous</p></div>
        <div><span><i class="fa-solid fa-file-medical"></i></span> <p class="dash-p">Ordonnances</p></div>
        <div><span><i class="fa-regular fa-address-book"></i> </span> <p class="dash-p">Contacts d'Urgence</p></div>
    </nav>
    <main class="dash-main">
        <header class="dash-header">
            <h2>Tableau de bord</h2>
            <div class="right-dash-header">
                <div class="notif">
                    <i class="fa-regular fa-bell"></i>
                </div>
                <p class="dash-p"><i class="fa-solid fa-user"></i> <span> <?= htmlspecialchars($_SESSION['user_name']) ?></span></p>
                
                <!--<a href="logout.php">Se déconnecter</a>-->
                <div class='change-color' id='changeColor'>
                <img src="./assets/images/moon.jpg" alt="">
                </div>
            </div>
        </header>
    
        <slider class="dash-slider">
            <article id="medicament">
                <div class="line">
                <div class="partie partie-1">
                    <h3>Ajouter un médicament</h3>
                    <form action="./ajouter_medicament.php" method="POST">
                        <label for="nom">Nom du médicament :</label><br>
                        <input type="text" id="nom" name="nom" required><br><br>

                        <label for="posologie">Posologie :</label><br>
                        <input type="text" id="posologie" name="posologie" required><br><br>

                        <label for="frequence">Fréquence (par jour) :</label><br>
                        <input type="number" id="frequence" name="frequence" min="1" required><br><br>

                        <label for="debut">Date de début :</label><br>
                        <input type="date" id="debut" name="debut" required><br><br>

                        <label for="fin">Date de fin :</label><br>
                        <input type="date" id="fin" name="fin"><br><br>

                        <input type="hidden" name="user_id" value="<?= $_SESSION['user_id']?>"><!-- à adapter dynamiquement -->

                        <button type="submit">Ajouter</button>
                    </form>
                </div> 
                <div class="partie partie-2">
                <h3>Consulter, modifier ou supprimer les médicaments enregistrés</h3>

                    <?php if (empty($medicaments)): ?>
                        <p>Aucun médicament à modifier ou supprimer.</p>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Posologie</th>
                                    <th>Fréquence</th>
                                    <th>Début</th>
                                    <th>Fin</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($medicaments as $med): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($med['nom']) ?></td>
                                        <td><?= htmlspecialchars($med['posologie']) ?></td>
                                        <td><?= htmlspecialchars($med['frequence']) ?></td>
                                        <td><?= htmlspecialchars($med['debut']) ?></td>
                                        <td><?= htmlspecialchars($med['fin'] ?? '-') ?></td>
                                        <td>
                                            <button type="button"
                                                onclick="ouvrirModal(<?= $med['id'] ?>, '<?= addslashes($med['nom']) ?>', '<?= addslashes($med['posologie']) ?>', <?= $med['frequence'] ?>, '<?= $med['debut'] ?>', '<?= $med['fin'] ?>')">
                                                Modifier
                                            </button>

                                            <form action="supprimer_medicament.php" method="POST" style="display:inline;" onsubmit="return confirm('Supprimer ce médicament ?');">
                                                <input type="hidden" name="id" value="<?= $med['id'] ?>">
                                                <button type="submit" style="color:red;">Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>   

            </article>
            <article id="redezvous" >
                
            </article>
            <article id="ordonnances">
                
            </article>
            <article id="contact">

            </article>
        </slider>
    </main>
    <div id="modalModifier" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:#00000099; justify-content:center; align-items:center;">
        <div style="background:white; padding:20px; border-radius:10px; width:400px;">
            <h3>Modifier le médicament</h3>
            <form id="formModifier" method="POST" action="modifier_medicament.php">
                <input type="hidden" name="id" id="modif_id">

                <label for="modif_nom">Nom :</label>
                <input type="text" name="nom" id="modif_nom" required><br><br>

                <label for="modif_posologie">Posologie :</label>
                <input type="text" name="posologie" id="modif_posologie" required><br><br>

                <label for="modif_frequence">Fréquence (par jour) :</label>
                <input type="number" name="frequence" id="modif_frequence" required min="1"><br><br>

                <label for="modif_debut">Date de début :</label>
                <input type="date" name="debut" id="modif_debut" required><br><br>

                <label for="modif_fin">Date de fin :</label>
                <input type="date" name="fin" id="modif_fin"><br><br>

                <button type="submit">Enregistrer</button>
                <button type="button" onclick="fermerModal()">Annuler</button>
            </form>
        </div>
    </div>
    <script src='assets/js/main.js'></script>
</body>
</html>
