<?php
function renderCreateEventPage(array $values, string $error, string $success, string $min_date, string $current_time): void {
    renderHeader('Event erstellen - Event-System', 'create_event');
    ?>
    <main class="container">
        <section class="hero-small">
            <div>
                <h1>Event erstellen</h1>
                <p>Lege ein neues Event an und teile es mit deiner Community.</p>
            </div>
        </section>

        <section class="form-section">
            <div class="form-container">
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data" class="event-form">
                    <div class="form-group">
                        <label for="title">Titel</label>
                        <input type="text" id="title" name="title" value="<?php echo $values['title']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Beschreibung</label>
                        <textarea id="description" name="description" rows="5"><?php echo $values['description']; ?></textarea>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="date">Datum</label>
                            <input type="date" id="date" name="date" value="<?php echo $values['date']; ?>" min="<?php echo $min_date; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="time">Uhrzeit</label>
                            <input type="time" id="time" name="time" value="<?php echo $values['time']; ?>" min="<?php echo $current_time; ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="location">Ort</label>
                        <input type="text" id="location" name="location" value="<?php echo $values['location']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="capacity">Kapazität</label>
                        <input type="number" id="capacity" name="capacity" value="<?php echo $values['capacity']; ?>" min="1" required>
                    </div>

                    <div class="form-group">
                        <label for="image">Event-Bild (optional)</label>
                        <input type="file" id="image" name="image" accept="image/jpeg,image/jpg,image/png,image/webp">
                    </div>

                    <button type="submit" class="btn-submit">Event speichern</button>
                </form>
            </div>
        </section>
    </main>
    <?php
    renderFooter();
}
