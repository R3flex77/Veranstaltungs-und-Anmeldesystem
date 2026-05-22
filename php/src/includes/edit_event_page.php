<?php
?>

<section class="hero-small">
    <div class="hero-content">
        <h1>✏️ EVENT BEARBEITEN</h1>
        <p>Ändere die Details deiner Veranstaltung</p>
    </div>
</section>

<div class="container">
    <div class="form-container">
        <div class="form-card">
            <h1 class="form-title">Event bearbeiten</h1>
            <p class="form-subtitle">Passe die Informationen deines Events an</p>

            <?php if ($success): ?>
                <div class="alert alert-success">✅ <?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error">❌ <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Event-Titel <span class="required">*</span></label>
                    <input type="text" name="title" placeholder="z.B. Sommerfest 2024" 
                           value="<?php echo htmlspecialchars($event['title']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Beschreibung</label>
                    <textarea name="description" placeholder="Beschreiben Sie Ihr Event..."><?php echo htmlspecialchars($event['description']); ?></textarea>
                </div>
                
                <div class="datetime-row">
                    <div class="form-group">
                        <label>Datum <span class="required">*</span></label>
                        <input type="date" name="date" min="<?php echo $min_date; ?>" 
                               value="<?php echo $event_date; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Uhrzeit <span class="required">*</span></label>
                        <input type="time" name="time" value="<?php echo $event_time; ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Ort <span class="required">*</span></label>
                    <input type="text" name="location" placeholder="z.B. Bürgerhaus, Online via Zoom, ..." 
                           value="<?php echo htmlspecialchars($event['location']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Event-Bild</label>
                    
                    <?php if (!empty($event['image']) && file_exists($event['image'])): ?>
                        <div class="current-image">
                            <label>Aktuelles Bild:</label>
                            <img src="<?php echo htmlspecialchars($event['image']); ?>" alt="Aktuelles Event-Bild">
                            <label class="image-checkbox">
                                <input type="checkbox" name="delete_image" value="1" onchange="toggleImageUpload()">
                                Bild löschen
                            </label>
                        </div>
                    <?php endif; ?>
                    
                    <div id="uploadArea" class="image-upload" onclick="document.getElementById('imageInput').click()">
                        <div class="upload-icon">🖼️</div>
                        <div class="upload-text">Klicken Sie hier, um ein neues Bild hochzuladen</div>
                        <div class="upload-hint">Empfohlen: 800x600px, JPG, PNG oder WEBP (max. 5MB)</div>
                    </div>
                    <input type="file" name="image" id="imageInput" class="file-input" accept="image/jpeg,image/jpg,image/png,image/webp" onchange="previewImage(this)">
                    <div class="image-preview" id="imagePreview">
                        <img id="previewImg" src="" alt="Vorschau">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Maximale Teilnehmeranzahl <span class="required">*</span></label>
                    <input type="number" name="capacity" min="1" max="9999" 
                           value="<?php echo $event['capacity']; ?>" required>
                    
                    <?php
                    $reg_query = "SELECT COUNT(*) as count FROM registrations WHERE event_id = :event_id";
                    $reg_stmt = $db->prepare($reg_query);
                    $reg_stmt->execute([':event_id' => $event_id]);
                    $current_registrations = $reg_stmt->fetch(PDO::FETCH_ASSOC)['count'];
                    ?>

                    <?php if ($current_registrations > 0): ?>
                        <small style="color: #ffc107; display: block; margin-top: 5px;">
                            ⚠️ Aktuell sind <?php echo $current_registrations; ?> Teilnehmer registriert. 
                            Die Kapazität kann nicht unter diese Zahl gesenkt werden.
                        </small>
                    <?php else: ?>
                        <small style="color: #666; display: block; margin-top: 5px;">Mindestens 1 Teilnehmer</small>
                    <?php endif; ?>
                </div>
                
                <div class="form-buttons">
                    <button type="submit" class="btn-submit">💾 ÄNDERUNGEN SPEICHERN</button>
                    <a href="dashboard.php" class="btn-cancel">❌ ABBRECHEN</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/js/edit_event.js"></script>
