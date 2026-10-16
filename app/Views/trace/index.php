<!-- Public Traceability Passport Card -->
<div class="trace-card">
    <div class="card-top-bar"></div>
    <div class="trace-card-content">
        <div class="section-label">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <span><?= t('verified_by_agindu') ?></span>
        </div>

        <div class="info-grid">
            <div class="info-item full-width">
                <div class="info-key"><?= t('batch_code') ?></div>
                <div class="info-val mono"><?= e($batch['batch_code']) ?></div>
            </div>

            <div class="info-item">
                <div class="info-key"><?= t('commodity') ?></div>
                <div class="info-val"><?= e($batch['commodity_name']) ?></div>
            </div>

            <div class="info-item">
                <div class="info-key"><?= t('status') ?></div>
                <div class="info-val">
                    <?php
                    $statusClass = 'badge-neutral';
                    $statusTransKey = 'status_' . $batch['status'];
                    if ($batch['status'] === 'in_transit' || $batch['status'] === 'shipped') {
                        $statusClass = 'badge-lime';
                    } elseif ($batch['status'] === 'completed') {
                        $statusClass = 'badge-green';
                    } elseif ($batch['status'] === 'processing') {
                        $statusClass = 'badge-amber';
                    }
                    ?>
                    <span class="status-pill <?= $statusClass ?>">
                        <?= t($statusTransKey) ?>
                    </span>
                </div>
            </div>

            <div class="info-item">
                <div class="info-key"><?= t('producer') ?></div>
                <div class="info-val"><?= e($batch['producer_name']) ?></div>
            </div>

            <div class="info-item">
                <div class="info-key"><?= t('origin') ?></div>
                <div class="info-val"><?= e($batch['origin_regency']) ?>, <?= e($batch['origin_province']) ?></div>
            </div>

            <div class="info-item">
                <div class="info-key"><?= t('harvest_date') ?></div>
                <div class="info-val"><?= format_date($batch['harvest_date'], 'd M Y') ?></div>
            </div>

            <div class="info-item">
                <div class="info-key"><?= t('volume') ?></div>
                <div class="info-val"><?= format_kg($batch['volume_kg']) ?></div>
            </div>

            <div class="info-item full-width">
                <div class="info-key"><?= t('destination') ?></div>
                <div class="info-val"><?= e($batch['destination_country_name']) ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Cold Chain & Temperature Monitoring Card -->
<div class="trace-card">
    <div class="trace-card-content">
        <div class="section-label">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path>
            </svg>
            <span><?= t('cold_chain') ?></span>
        </div>

        <?php if ($latestTemp): ?>
        <div class="temp-gauge-box">
            <div class="temp-gauge-left">
                <div class="info-key"><?= t('latest_reading') ?></div>
                <div class="temp-huge"><?= e($latestTemp['temperature']) ?> &deg;C</div>
                <div class="temp-range-text">
                    <?= t('safe_range') ?>: <?= e($batch['temp_min']) ?> &deg;C &ndash; <?= e($batch['temp_max']) ?> &deg;C
                </div>
            </div>

            <div style="text-align: right;">
                <span class="temp-status-tag <?= $tempStatus ?>">
                    <?= t('condition_' . $tempStatus) ?>
                </span>
                <div style="font-size: 10.5px; color: var(--muted); margin-top: 6px;">
                    <?= format_date($latestTemp['recorded_at']) ?>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div style="text-align: center; color: var(--muted); padding: 16px; font-size: 13px;">
            <?= t('condition_nodata') ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Logistics Journey Timeline Card -->
<div class="trace-card">
    <div class="trace-card-content">
        <div class="section-label">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon>
                <line x1="8" y1="2" x2="8" y2="18"></line>
                <line x1="16" y1="6" x2="16" y2="22"></line>
            </svg>
            <span><?= t('journey_timeline') ?></span>
        </div>

        <?php if (empty($timelineEvents)): ?>
        <div style="text-align: center; color: var(--muted); padding: 18px; font-size: 13px;">
            <?= t('no_timeline') ?>
        </div>
        <?php else: ?>
        <div class="public-timeline">
            <?php foreach ($timelineEvents as $idx => $event): ?>
            <?php $isLast = ($idx === count($timelineEvents) - 1); ?>
            <div class="timeline-step">
                <div class="step-marker <?= $isLast ? 'active' : '' ?>"></div>
                <div class="step-content">
                    <div class="step-title"><?= e($event['title']) ?></div>
                    <div class="step-location"><?= e($event['location']) ?></div>
                    <div class="step-time"><?= format_date($event['recorded_at']) ?></div>
                    <?php if (!empty($event['description'])): ?>
                    <div style="font-size: 11.5px; color: #44564b; margin-top: 4px; line-height: 1.4;">
                        <?= e($event['description']) ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
