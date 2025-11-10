<?php
require_once __DIR__ . '/../../../config/config.php';
$baseUrl = BASE_URL;
$currentUser = $user ?? null;
$stats = $stats ?? [];
$history = $history ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsletter - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>css/dashboard_admin.css">
    <style>
        .editor-toolbar {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-bottom: none;
            border-radius: 8px 8px 0 0;
            padding: 10px;
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        .editor-btn {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 8px 12px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .editor-btn:hover {
            background: #e9ecef;
        }
        .editor-btn i {
            font-size: 16px;
        }
        #message {
            border-radius: 0 0 8px 8px;
            border-top: none;
            min-height: 300px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .preview-container {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            background: #f8f9fa;
            max-height: 600px;
            overflow-y: auto;
        }
        .history-item {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid var(--accent-blue);
            transition: all 0.3s;
        }
        .history-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .badge-recipient {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .template-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid #e9ecef;
        }
        .template-card:hover {
            border-color: var(--accent-blue);
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        .template-card.selected {
            border-color: var(--accent-blue);
            background: rgba(19, 163, 174, 0.05);
        }
    </style>
</head>
<body>
    <?php
    $currentPage = 'newsletter';
    $userName = $currentUser['name'] ?? ($currentUser['nom'] ?? 'Admin');
    include __DIR__ . '/sidebar.php';
    ?>
    
    <div class="main-content">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="dashboard-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h1 class="card-title mb-0">
                                <i class="bi bi-envelope-paper me-2"></i>
                                Newsletter Manuelle
                            </h1>
                            <a href="<?php echo $baseUrl; ?>auto_newsletter" class="btn btn-outline-success">
                                <i class="bi bi-robot me-2"></i>
                                Newsletter Automatique
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Bloc Test SMTP -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="alert alert-info d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Testez votre configuration SMTP</strong>
                            <small class="d-block text-muted">Vérifiez que vos emails peuvent être envoyés avant d'utiliser la newsletter</small>
                        </div>
                        <a href="<?php echo $baseUrl; ?>newsletter/testSMTP" class="btn btn-sm btn-primary">
                            <i class="bi bi-tools me-1"></i>Tester SMTP
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="stat-number"><?php echo $stats['total_subscribers'] ?? 0; ?></div>
                        <div class="stat-label">Total Abonnés</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <div class="stat-number"><?php echo $stats['total_clients'] ?? 0; ?></div>
                        <div class="stat-label">Clients</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe, #00f2fe);">
                            <i class="bi bi-briefcase"></i>
                        </div>
                        <div class="stat-number"><?php echo $stats['total_managers'] ?? 0; ?></div>
                        <div class="stat-label">Gestionnaires</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b, #38f9d7);">
                            <i class="bi bi-send-check"></i>
                        </div>
                        <div class="stat-number"><?php echo $stats['total_sent'] ?? 0; ?></div>
                        <div class="stat-label">Envois Total</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Formulaire d'envoi -->
                <div class="col-lg-8 mb-4">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2 class="card-title mb-0">
                                <i class="bi bi-pencil-square me-2"></i>
                                Composer Newsletter
                            </h2>
                        </div>
                        <div class="card-body p-4">
                            <form action="<?php echo $baseUrl; ?>newsletter/send" method="POST" id="newsletterForm">
                                <!-- Destinataires -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-people me-2"></i>Destinataires
                                    </label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="recipient_type" 
                                                   id="recipientAll" value="all" checked>
                                            <label class="form-check-label" for="recipientAll">
                                                Tous les utilisateurs
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="recipient_type" 
                                                   id="recipientClients" value="clients">
                                            <label class="form-check-label" for="recipientClients">
                                                Clients uniquement
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="recipient_type" 
                                                   id="recipientManagers" value="managers">
                                            <label class="form-check-label" for="recipientManagers">
                                                Gestionnaires uniquement
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sujet -->
                                <div class="mb-4">
                                    <label for="subject" class="form-label fw-bold">
                                        <i class="bi bi-chat-left-text me-2"></i>Sujet
                                    </label>
                                    <input type="text" class="form-control" id="subject" name="subject" 
                                           placeholder="Ex: Nouveaux terrains disponibles ce week-end !" required>
                                </div>

                                <!-- Message -->
                                <div class="mb-4">
                                    <label for="message" class="form-label fw-bold">
                                        <i class="bi bi-file-text me-2"></i>Message
                                    </label>
                                    <div class="editor-toolbar">
                                        <button type="button" class="editor-btn" onclick="formatText('bold')" title="Gras">
                                            <i class="bi bi-type-bold"></i>
                                        </button>
                                        <button type="button" class="editor-btn" onclick="formatText('italic')" title="Italique">
                                            <i class="bi bi-type-italic"></i>
                                        </button>
                                        <button type="button" class="editor-btn" onclick="formatText('underline')" title="Souligné">
                                            <i class="bi bi-type-underline"></i>
                                        </button>
                                        <span class="mx-2">|</span>
                                        <button type="button" class="editor-btn" onclick="insertTemplate('promo')" title="Template Promo">
                                            <i class="bi bi-tag"></i>
                                        </button>
                                        <button type="button" class="editor-btn" onclick="insertTemplate('event')" title="Template Événement">
                                            <i class="bi bi-calendar-event"></i>
                                        </button>
                                        <button type="button" class="editor-btn" onclick="insertTemplate('welcome')" title="Template Bienvenue">
                                            <i class="bi bi-hand-thumbs-up"></i>
                                        </button>
                                    </div>
                                    <textarea class="form-control" id="message" name="message" rows="12" 
                                              placeholder="Rédigez votre message ici..." required></textarea>
                                </div>

                                <!-- Actions -->
                                <div class="d-flex gap-3 justify-content-end">
                                    <button type="button" class="btn btn-outline-primary" onclick="previewNewsletter()">
                                        <i class="bi bi-eye me-2"></i>Aperçu
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-send me-2"></i>Envoyer la Newsletter
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Historique -->
                <div class="col-lg-4 mb-4">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2 class="card-title mb-0">
                                <i class="bi bi-clock-history me-2"></i>
                                Historique
                            </h2>
                        </div>
                        <div class="card-body p-3" style="max-height: 700px; overflow-y: auto;">
                            <?php if (!empty($history)): ?>
                                <?php foreach ($history as $item): ?>
                                    <div class="history-item">
                                        <h6 class="mb-2 fw-bold"><?php echo htmlspecialchars($item['subject']); ?></h6>
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <span class="badge-recipient bg-primary text-white">
                                                <?php 
                                                $types = [
                                                    'all' => 'Tous',
                                                    'clients' => 'Clients',
                                                    'managers' => 'Gestionnaires'
                                                ];
                                                echo $types[$item['recipient_type']] ?? 'Tous';
                                                ?>
                                            </span>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                <?php echo date('d/m/Y H:i', strtotime($item['created_at'])); ?>
                                            </small>
                                        </div>
                                        <div class="d-flex gap-3">
                                            <small class="text-success">
                                                <i class="bi bi-check-circle me-1"></i>
                                                <?php echo $item['sent_count']; ?> envoyés
                                            </small>
                                            <?php if ($item['failed_count'] > 0): ?>
                                                <small class="text-danger">
                                                    <i class="bi bi-x-circle me-1"></i>
                                                    <?php echo $item['failed_count']; ?> échecs
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center text-muted py-5">
                                    <i class="bi bi-inbox display-4 mb-3"></i>
                                    <p>Aucune newsletter envoyée</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Aperçu -->
    <div class="modal fade" id="previewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-eye me-2"></i>Aperçu de la Newsletter
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="previewContent" class="preview-container"></div>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Templates de messages
        const templates = {
            promo: `🎉 OFFRE SPÉCIALE ! 🎉

Profitez de -20% sur toutes vos réservations ce week-end !

Utilisez le code promo: WEEKEND20

Réservez dès maintenant et économisez sur votre prochain match !`,
            
            event: `🏆 GRAND TOURNOI À VENIR ! 🏆

Nous organisons un tournoi exceptionnel le [DATE].

Places limitées !
Inscriptions ouvertes dès maintenant.

Rejoignez-nous pour une journée inoubliable !`,
            
            welcome: `🤝 Bienvenue sur Book&Play !

Nous sommes ravis de vous compter parmi nous.

Découvrez nos terrains de qualité et réservez en quelques clics.

À très bientôt sur nos terrains !`
        };

        function insertTemplate(type) {
            const messageField = document.getElementById('message');
            messageField.value = templates[type];
        }

        function formatText(command) {
            const messageField = document.getElementById('message');
            const start = messageField.selectionStart;
            const end = messageField.selectionEnd;
            const selectedText = messageField.value.substring(start, end);
            
            if (selectedText) {
                let formattedText = selectedText;
                
                switch(command) {
                    case 'bold':
                        formattedText = `<strong>${selectedText}</strong>`;
                        break;
                    case 'italic':
                        formattedText = `<em>${selectedText}</em>`;
                        break;
                    case 'underline':
                        formattedText = `<u>${selectedText}</u>`;
                        break;
                }
                
                messageField.value = messageField.value.substring(0, start) + formattedText + messageField.value.substring(end);
            }
        }

        function previewNewsletter() {
            const subject = document.getElementById('subject').value;
            const message = document.getElementById('message').value;
            
            if (!subject || !message) {
                alert('Veuillez remplir le sujet et le message');
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo $baseUrl; ?>newsletter/preview';
            form.target = '_blank';
            
            const subjectInput = document.createElement('input');
            subjectInput.type = 'hidden';
            subjectInput.name = 'subject';
            subjectInput.value = subject;
            form.appendChild(subjectInput);
            
            const messageInput = document.createElement('input');
            messageInput.type = 'hidden';
            messageInput.name = 'message';
            messageInput.value = message;
            form.appendChild(messageInput);
            
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }

        // Confirmation avant envoi
        document.getElementById('newsletterForm').addEventListener('submit', function(e) {
            if (!confirm('Êtes-vous sûr de vouloir envoyer cette newsletter ?')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>