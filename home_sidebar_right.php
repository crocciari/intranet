<!-- SIDEBAR DIREITA -->
<div class="col-lg-3 col-md-12">
    <!-- Status -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-circle-fill me-2" style="color: #00c853;"></i>System Status
        </div>
        <div class="card-body">
            <div class="d-flex align-items-center">
                <span class="status-dot online"></span>
                <span>Online</span>
                <span class="ms-auto text-secondary">● 100%</span>
            </div>
        </div>
    </div>

    <!-- Gerador de Senha -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-shield-lock me-2"></i>Password Generator
        </div>
        <div class="card-body">
            <!-- UUID -->
            <div class="password-demo">
                <h6><i class="bi bi-hash me-2 text-primary"></i>Random UUID</h6>
                <div class="pre-box">
                    <?= gerarUUID() ?>
                </div>
            </div>

            <!-- GERADOR DE SENHA -->
            <div class="password-demo">
                <h6><i class="bi bi-shield-lock me-2 text-primary"></i>Password Generator</h6>
                <div class="row g-2">
                    <div class="col-12">
                        <strong>All chars:</strong>
                        <div class="pre-box"><?= gerarStringPersonalizada(10) ?></div>
                    </div>
                    <div class="col-6">
                        <strong>No symbols:</strong>
                        <div class="pre-box"><?= gerarStringPersonalizada(10, true, true, true, false) ?></div>
                    </div>
                    <div class="col-6">
                        <strong>Only letters:</strong>
                        <div class="pre-box"><?= gerarStringPersonalizada(10, true, true, false, false) ?></div>
                    </div>
                </div>
            </div>

            <!-- SUGESTÃO DE SENHA -->
            <div class="password-demo">
                <h6><i class="bi bi-key me-2 text-primary"></i>Password Suggestion</h6>
                <div class="pre-box">
                    <?= gerarStringPersonalizada(8) ?>@<?= gerarStringPersonalizada(8) ?>@<?= gerarStringPersonalizada(4) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Password Hash Demo -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-lock me-2"></i>Password & Hash
        </div>
        <div class="card-body">
            <?php
            $password = gerarStringPersonalizada(8);
            $hash = passwd($password);
            $verify = passwd_verify($password, $hash);
            $rehash = passwd_rehash($hash);
            ?>
            <div class="row g-2">
                <div class="col-12">
                    <strong>Password:</strong>
                    <div class="pre-box"><?= $password ?></div>
                </div>
                <div class="col-12">
                    <strong>Hash:</strong>
                    <div class="pre-box" style="font-size: 11px;"><?= $hash ?></div>
                </div>
                <div class="col-6">
                    <strong>Verify:</strong>
                    <span class="badge <?= $verify ? 'badge-success' : 'badge-danger' ?>">
                        <?= $verify ? '✅ Valid' : '❌ Invalid' ?>
                    </span>
                </div>
                <div class="col-6">
                    <strong>Rehash:</strong>
                    <span class="badge <?= $rehash ? 'badge-warning' : 'badge-info' ?>">
                        <?= $rehash ? '🔄 Needed' : '✅ Updated' ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>