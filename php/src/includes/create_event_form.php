<div class="form-card">
    <h1 class="form-title">Event Details</h1>
    <p class="form-subtitle">Füllen Sie die Informationen Ihres Events aus</p>
    
    <?php if ($error): ?>
        <div class="alert alert-error">❌ <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if ($success && isset($_POST['title'])): ?>
        <div class="alert alert-success">✅ <?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label>Event-Titel <span class="required">*</span></label>
            <input type="text" name="title" placeholder="z.B. Sommerfest 2024" 
                   value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label>Beschreibung</label>
            <textarea name="description" placeholder="Beschreiben Sie Ihr Event..."><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
        </div>
        
        <div class="datetime-row">
            <div class="form-group">
                <label>Datum <span class="required">*</span></label>
                <input type="date" name="date" min="<?php echo $min_date; ?>" 
                       value="<?php echo isset($_POST['date']) ? htmlspecialchars($_POST['date']) : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Uhrzeit <span class="required">*</span></label>
                <input type="time" name="time" 
                       value="<?php echo isset($_POST['time']) ? htmlspecialchars($_POST['time']) : $current_time; ?>" required>
            </div>
        </div>
        
        <div class="form-group">
            <label>Ort <span class="required">*</span></label>
            <input type="text" name="location" placeholder="z.B. Bürgerhaus, Online via Zoom, ..." 
                   value="<?php echo isset($_POST['location']) ? htmlspecialchars($_POST['location']) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label>Event-Bild</label>
            <div class="image-upload" onclick="document.getElementById('imageInput').click()">
                <div class="upload-icon">🖼️</div>
                <div class="upload-text">Klicken Sie hier, um ein Bild hochzuladen</div>
                <div class="upload-hint">Empfohlen: 800x600px, JPG, PNG oder WEBP (max. 5MB)</div>
            </div>
            <input type="file" name="image" id="imageInput" class="file-input" accept="image/jpeg,image/jpg,image/png,image/webp" onchange="previewImage(this)">
            <div class="image-preview" id="imagePreview">
                <img id="previewImg" src="" alt="Vorschau">
                <button type="button" class="remove-image" onclick="removeImage()">×</button>
            </div>
        </div>
        
        <div class="form-group">
            <label>Maximale Teilnehmeranzahl <span class="required">*</span></label>
            <input type="number" name="capacity" min="1" max="9999" 
                   value="<?php echo isset($_POST['capacity']) ? htmlspecialchars($_POST['capacity']) : '50'; ?>" required>
            <small style="color: #666; display: block; margin-top: 5px;">Mindestens 1 Teilnehmer</small>
        </div>
        
        <div class="form-buttons">
            <button type="submit" class="btn-submit">🚀 EVENT ERSTELLEN</button>
            <a href="index.php" class="btn-cancel">❌ ABBRECHEN</a>
        </div>
    </form>
</div>
