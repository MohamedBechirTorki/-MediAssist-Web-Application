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
$temps = [];
$ids = array_column($medicaments, 'id');
if (!empty($ids)) {
    $in = implode(',', array_map('intval', $ids));
    $result = $mysqli->query("SELECT medicament_id, valeur FROM temps WHERE medicament_id IN ($in)");

    while ($row = $result->fetch_assoc()) {
        $temps[$row['medicament_id']][] = $row['valeur'];
    }
}

// Handling the appointment form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_appointment'])) {
    $date = $_POST['date'];
    $heure = $_POST['heure'];
    $type_consultation = $_POST['type_consultation'];
    $lieu = $_POST['lieu'];
    $note = $_POST['note'];

    $stmt = $mysqli->prepare("INSERT INTO rendezvous (user_id, date, heure, type_consultation, lieu, note) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $user_id, $date, $heure, $type_consultation, $lieu, $note);
    $stmt->execute();
    echo "<p>Rendez-vous ajouté avec succès!</p>";
}

// Fetch appointments for the user
$stmt = $mysqli->prepare("SELECT * FROM rendezvous WHERE user_id = ? ORDER BY date, heure");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$appointments_result = $stmt->get_result();
$appointments = [];
while ($row = $appointments_result->fetch_assoc()) {
    $appointments[] = $row;
}



$appointmentDates = array_map(function($appointment) {
    return $appointment['date'];  // Assuming `date` is in 'YYYY-MM-DD' format
}, $appointments);

// Encode this array in JSON format to pass it to JavaScript
$appointmentDatesJson = json_encode($appointmentDates);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord - MediAssist</title>
    
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@3.9.0/dist/fullcalendar.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.9.0/dist/fullcalendar.min.js"></script>
    

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
                
                <p class="dash-p"><i class="fa-solid fa-user"></i> <span> <?= htmlspecialchars($_SESSION['user_name']) ?></span></p>
                <div>
                <a href="logout.php" style="text-decoration: none;">Se deconnecter <i class="fa-solid fa-right-from-bracket"></i></a>
                </div>
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

                            <label for="debut">Date de début :</label><br>
                            <input type="date" id="debut" name="debut" required><br><br>

                            <label for="fin">Date de fin :</label><br>
                            <input type="date" id="fin" name="fin"><br><br>

                            <label>Heures de prise :</label><br>
                            <div id="temps-container">
                                <div class="temps-line">
                                    <input type="time" name="temps[]">
                                    <button type="button" class="btn-supprimer" style="padding: 10px;" onclick="supprimerChampTemps(this)">−</button> <br>
                                </div>
                            </div>
                            <br>
                            <button type="button" style="font-size:12px; padding:7.5px 13.5px" class="btn-ajouter" onclick="ajouterChampTemps()">Ajouter une autre heure</button><br><br>

                            <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
                        <button class="btn-ajouter" type="submit">Ajouter</button>
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
                                        <th>Heures de prise</th>
                                        <th>Début</th>
                                        <th>Fin</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($medicaments as $med): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($med['nom']) ?></td>
                                            <td>
                                                <?php
                                                if (!empty($temps[$med['id']])) {
                                                    echo implode(', ', array_map('htmlspecialchars', $temps[$med['id']]));
                                                } else {
                                                    echo '-';
                                                }
                                                ?>
                                            </td>
                                            <td><?= htmlspecialchars($med['debut']) ?></td>
                                            <td><?= htmlspecialchars($med['fin'] ?? '-') ?></td>
                                            <td>
                                                <button type="button" class="btn-modifier"  onclick="ouvrirModal(<?= $med['id'] ?>)">Modifier</button>
                                                <form action="supprimer_medicament.php" method="POST" style="display:inline;" onsubmit="return confirm('Supprimer ce médicament ?');">
                                                    <input type="hidden" name="id" value="<?= $med['id'] ?>">
                                                    <button class="btn-supprimer" type="submit">Supprimer</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </article>

            <!-- Appointment Section -->
            <article id="redezvous">
                <h2>Gestion des Rendez-vous Médicaux</h2>
                                                <br>
                <!-- Form to Add Appointment -->
                <form id="appointmentForm" method="POST">
                    <label for="date">Date:</label>
                    <input type="date" name="date" required><br><br>

                    <label for="heure">Heure:</label>
                    <input type="time" name="heure" required><br><br>

                    <label for="type_consultation">Type de Consultation:</label>
                    <input type="text" name="type_consultation" required><br><br>

                    <label for="lieu">Lieu:</label>
                    <input type="text" name="lieu" required><br><br>

                    <label for="note">Note:</label><br>
                    <textarea name="note"></textarea><br><br>

                    <input class="btn-ajouter" type="submit" name="add_appointment" value="Ajouter Rendez-vous">
                </form>
                <br><br>

                <!-- Table to Display Appointments -->
                <h3>Vos Rendez-vous</h3>
                <table id="appointmentsTable" style="width: 90%">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Heure</th>
                        <th>Type de Consultation</th>
                        <th>Lieu</th>
                        <th>Note</th>
                    </tr>
                    </thead>
                    <tbody id="appointments-table">
                    <?php foreach ($appointments as $appt): ?>
                        <tr class="appointment-row-<?= $appt['id'] ?>">
                            <td><?= $appt['date'] ?></td>
                            <td><?= $appt['heure'] ?></td>
                            <td><?= $appt['type_consultation'] ?></td>
                            <td><?= $appt['lieu'] ?></td>
                            <td><?= $appt['note'] ?></td>
                            <td><button class="btn-supprimer delete-appointment" data-id="<?= $appt['id'] ?>">Supprimer</button></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                </table>
                    <br><br>
                <h3>Calendrier des Rendez-vous</h3>
                <div id="calendar-container" style="padding: 20px;">
                    <div id="calendar" style="max-width: 1000px; margin: auto;"></div>
                </div>

    
            </article>


            <article id="ordonnances">
            <form id="ordonnanceForm" enctype="multipart/form-data">
                <h2>Ajouter une ordonnance</h2>
                <br>
                <label for="image_ordonnance" class="btn-choisir-fichier">📂 Choisir une ordonnance</label>
                <input type="file" id="image_ordonnance" name="image_ordonnance" accept="image/*" required style="display: none;"><br><br>
                <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">       
                <input class="btn-ajouter" type="submit" value="Ajouter">
            </form>
            <div id="responseMessage"></div>
            <h2>Votre ordonnances</h2>
            <div id="aff-ordonnances">
        <?php
        // Fetch the ordonnances for the current user
        $stmt = $mysqli->prepare("SELECT * FROM ordonnances WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $ordonnances_result = $stmt->get_result();
        if ($ordonnances_result->num_rows > 0) {
            while ($row = $ordonnances_result->fetch_assoc()) {
                $id = $row['id'];
                $filePath = "uploads/ordonnances/" . htmlspecialchars($row['nom_fichier']);
                echo "<div class='ordonnance-item' id='ordonnance-$id'>";
                echo "<img src='$filePath' alt='Ordonnance' style='max-width: 200px;' class='ordonnance-thumbnail' onclick='afficherImagePleinEcran(this.src)><br>";
                echo "<p><strong>Nom du fichier:</strong> " . htmlspecialchars($row['nom_fichier']) . "</p>";
                echo "<p><strong>Date d'upload:</strong> " . htmlspecialchars($row['date_upload']) . "</p>";
                echo "<button class='btn-supprimer' onclick='supprimerOrdonnance($id)'>Supprimer</button>";
                echo "</div>";
            }
        } else {
            echo "<p>Aucune ordonnance à afficher.</p>";
        }
        
        ?>
    </div>
            <?php
            ?>
        <div id="image-popup" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.8); justify-content:center; align-items:center; z-index:1000;">
    <img id="popup-image" src="" style="max-width:90%; max-height:90%;">
</div>
<script>
function afficherImagePleinEcran(src) {
    const popup = document.getElementById('image-popup');
    const img = document.getElementById('popup-image');
    img.src = src;
    popup.style.display = 'flex';
}

document.getElementById('image-popup').addEventListener('click', function () {
    this.style.display = 'none'; // Clic pour fermer
});
</script>

     </article>

            <script>
                document.getElementById('ordonnanceForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = document.getElementById('ordonnanceForm');
    const formData = new FormData(form);

    fetch('ajouter_ordonnance.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(html => {
        // Ajouter la nouvelle ordonnance à la liste
        document.getElementById('aff-ordonnances').insertAdjacentHTML('afterbegin', html);

        // Message de succès
        document.getElementById('responseMessage').textContent = "Ordonnance ajoutée avec succès.";
        document.getElementById('responseMessage').style.color = 'green';

        // Réinitialiser le formulaire
        form.reset();
    })
    .catch(error => {
        console.error('Erreur:', error);
        document.getElementById('responseMessage').textContent = "Erreur lors de l'ajout.";
        document.getElementById('responseMessage').style.color = 'red';
    });
});
                function supprimerOrdonnance(id) {
    if (!confirm("Êtes-vous sûr de vouloir supprimer cette ordonnance ?")) return;

    fetch('supprimer_ordonnance.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'id=' + encodeURIComponent(id)
    })
    .then(response => {
        if (response.ok) {
            // Supprimer l'élément du DOM
            const element = document.getElementById('ordonnance-' + id);
            if (element) element.remove();
        } else {
            alert("Une erreur est survenue lors de la suppression.");
        }
    })
    .catch(error => {
        console.error("Erreur AJAX:", error);
        alert("Erreur de communication avec le serveur.");
    });
}
$(document).on('click', '.delete-ordonnance', function () {
    const id = $(this).data('id');
    if (confirm('Supprimer cette ordonnance ?')) {
        $.ajax({
            url: 'supprimer_ordonnance.php',
            type: 'POST',
            data: { id: id },
            success: function(response) {
                if (response === 'success') {
                    $('#ord-' + id).remove();
                } else {
                    alert('Erreur lors de la suppression.');
                }
            },
            error: function() {
                alert('Erreur serveur.');
            }
        });
    }
});
</script>


        <article id="contact">
            <h2>Ajouter un contact d'urgence</h2>
            <form id="contactForm">
                <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
                <label for="nom_contact">Nom :</label><br>
                <input type="text" id="nom_contact" name="nom" required><br>

                <label for="tel_contact">Téléphone :</label><br>
                <input type="text" id="tel_contact" name="telephone" required><br><br>

                <input class="btn-ajouter" type="submit" value="Ajouter">
            </form>

            <div id="contactMessage"></div>

            <h2>Contacts d'urgence</h2>
            <div id="contactList">
                <?php
                $stmt = $mysqli->prepare("SELECT * FROM contacts_urgence WHERE user_id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='contact-item' id='contact-{$row['id']}'>";
                    echo "<p><strong>Nom:</strong> " . htmlspecialchars($row['nom']) . "</p>";
                    echo "<p><strong>Téléphone:</strong> " . htmlspecialchars($row['telephone']) . "</p>";
                    echo "<button class='btn-supprimer' onclick='supprimerContact({$row['id']})'>Supprimer</button>";
                    echo "</div>";
                }
                ?>
            </div>
        </article>
        <script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = document.getElementById('contactForm');
    const formData = new FormData(form);

    fetch('ajouter_contact.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(html => {
        document.getElementById('contactList').insertAdjacentHTML('afterbegin', html);
        document.getElementById('contactMessage').textContent = "Contact ajouté avec succès.";
        document.getElementById('contactMessage').style.color = 'green';
        form.reset();
    })
    .catch(err => {
        document.getElementById('contactMessage').textContent = "Erreur lors de l'ajout.";
        document.getElementById('contactMessage').style.color = 'red';
    });
});

function supprimerContact(id) {
    if (!confirm("Supprimer ce contact ?")) return;

    const formData = new FormData();
    formData.append("id", id);

    fetch('supprimer_contact.php', {
        method: 'POST',
        body: formData
    })
    .then(() => {
        document.getElementById("contact-" + id).remove();
    })
    .catch(err => alert("Erreur de suppression"));
}
</script>

                </slider>
    </main>

    <script>
        $(document).ready(function() {
        $('#ordonnanceForm').on('submit', function(e) {
            e.preventDefault();  // Prevent the default form submission

            var formData = new FormData(this);

            $.ajax({
                url: 'upload_ordonnance.php',  // Your PHP script that handles the upload
                type: 'POST',
                data: formData,
                contentType: false,  // Let jQuery set the content type for FormData
                processData: false,  // Don't process the data
                success: function(response) {
                    $('#responseMessage').html(response);  // Display the plain response message
                },
                error: function() {
                    $('#responseMessage').html('<p class="error">Erreur lors de l\'envoi du fichier.</p>');
                }
            });
        });
    });

    </script>

    <script>
        $('#appointmentForm').submit(function(e) {
    e.preventDefault();
    const form = $(this);

    $.ajax({
        url: 'ajouter_rendezvous.php',
        type: 'POST',
        data: form.serialize(),
        success: function(res) {
            try {
                const data = JSON.parse(res);
                if (data.success) {
                    const appt = data.appointment;
                    // Supprimer les événements avec ce même ID s'ils existent (précaution)
                    $('#calendar').fullCalendar('removeEvents', appt.id);

                    // Ajouter dans le calendrier
                    $('#calendar').fullCalendar('renderEvent', {
                        id: appt.id,
                        title: 'Rendez-vous',
                        start: appt.date,
                        backgroundColor: '#ff9999',
                        borderColor: '#ff6666'
                    }, true);

                    // Ajouter à la table (évite duplication si existant)
                    $('#appointments-table').append(`
                        <tr class="appointment-row-${appt.id}">
                            <td>${appt.date}</td>
                            <td>${appt.heure}</td>
                            <td>${appt.type_consultation}</td>
                            <td>${appt.lieu}</td>
                            <td>${appt.note}</td>
                            <td><button class="btn-supprimer delete-appointment" data-id="${appt.id}">Supprimer</button></td>
                        </tr>
                    `);

                    // Réinitialiser le formulaire
                    form[0].reset();
                } else {
                    alert('Erreur lors de l\'ajout du rendez-vous');
                }
            } catch (err) {
                console.error('Erreur parsing JSON:', err);
                alert('Erreur inconnue, réponse serveur invalide');
            }
        },
        error: function() {
            alert('Erreur serveur');
        }
    });
});

$(document).ready(function() {
    // Initialize FullCalendar
    const calendar = $('#calendar').fullCalendar({
        events: [
            <?php foreach ($appointments as $appointment): ?>
                {
                    id: '<?= $appointment['id'] ?>',
                    title: 'Rendez-vous',
                    start: '<?= $appointment['date'] ?>T<?= $appointment['heure'] ?>',
                    backgroundColor: '#ff9999',
                    borderColor: '#ff6666'
                },
            <?php endforeach; ?>
        ]
    });

    


    // Handle appointment deletion (delegated event for future buttons)
    $(document).on('click', '.delete-appointment', function() {
        const appointmentId = $(this).data('id');

        if (confirm('Supprimer ce rendez-vous ?')) {
            $.ajax({
                url: 'supprimer_rendezvous.php',
                type: 'POST',
                data: { appointment_id: appointmentId },
                success: function(response) {
                    // Remove from calendar
                    $('#calendar').fullCalendar('removeEvents', appointmentId);

                    // Remove from table
                    $('.appointment-row-' + appointmentId).remove();

                    alert('Rendez-vous supprimé avec succès!');
                },
                error: function() {
                    alert('Erreur lors de la suppression du rendez-vous.');
                }
            });
        }
    });
});
</script>


    <script src='assets/js/main.js'></script>
</
