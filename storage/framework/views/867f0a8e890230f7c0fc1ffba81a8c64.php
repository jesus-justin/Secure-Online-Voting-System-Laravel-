

<?php $__env->startSection('title', 'Dashboard - Secure Voting'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-5">
    <!-- Welcome Header -->
    <div class="row mb-5">
        <div class="col-md-8">
            <h1 class="display-5 fw-bold text-dark">
                <i class="bi bi-house-check"></i> Welcome Back, <?php echo e(auth()->user()->name); ?>!
            </h1>
            <p class="lead text-muted">
                <?php if(auth()->user()->verified_at): ?>
                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Verified User</span>
                <?php else: ?>
                    <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle"></i> Pending Verification</span>
                <?php endif; ?>
                <?php
                    $lastLogin = auth()->user()->last_login_at;
                ?>
                <span class="badge bg-info ms-2"><i class="bi bi-clock"></i> Last login: <?php echo e($lastLogin ? $lastLogin->diffForHumans() : 'First time'); ?></span>
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="<?php echo e(route('logout')); ?>" class="btn btn-outline-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
            <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
                <?php echo csrf_field(); ?>
            </form>
        </div>
    </div>

    <!-- Warning Alert for Unverified Users -->
    <?php if(!auth()->user()->verified_at): ?>
        <div class="alert alert-warning alert-dismissible fade show mb-5" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <strong>Account Not Verified!</strong> Your account is pending admin verification. You'll be able to vote once your account is verified.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Quick Stats -->
    <div class="row g-4 mb-5">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow h-100 bg-primary text-white">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title opacity-75">Active Elections</h6>
                        <h2 class="mb-0 fw-bold"><?php echo e($activeElections ?? 0); ?></h2>
                    </div>
                    <div style="font-size: 3rem; opacity: 0.3;">
                        <i class="bi bi-ballot-check"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow h-100 bg-success text-white">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title opacity-75">Votes Cast</h6>
                        <h2 class="mb-0 fw-bold"><?php echo e($castVotes ?? 0); ?></h2>
                    </div>
                    <div style="font-size: 3rem; opacity: 0.3;">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow h-100 bg-info text-white">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title opacity-75">Completed Elections</h6>
                        <h2 class="mb-0 fw-bold"><?php echo e($completedElections ?? 0); ?></h2>
                    </div>
                    <div style="font-size: 3rem; opacity: 0.3;">
                        <i class="bi bi-graph-up"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow h-100 bg-warning text-dark">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="card-title opacity-75">Upcoming Elections</h6>
                        <h2 class="mb-0 fw-bold"><?php echo e($upcomingElections ?? 0); ?></h2>
                    </div>
                    <div style="font-size: 3rem; opacity: 0.3;">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row g-4">
        <!-- Active Elections -->
        <div class="col-lg-8">
            <div class="card border-0 shadow">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-list-ul"></i> Available Elections</h5>
                </div>
                <div class="card-body p-0">
                    <?php $__empty_1 = true; $__currentLoopData = ($elections ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $election): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php if($election->status == 'active'): ?>
                            <div class="p-4 border-bottom">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h6 class="mb-1 fw-bold"><?php echo e($election->title); ?></h6>
                                        <p class="text-muted small mb-2"><?php echo e($election->description); ?></p>
                                        <div class="d-flex gap-3">
                                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Active</span>
                                            <span class="text-muted small">
                                                <i class="bi bi-people"></i> <?php echo e($election->candidates->count()); ?> Candidates
                                            </span>
                                            <span class="text-muted small">
                                                <i class="bi bi-graph-up"></i> <?php echo e($election->votes->count()); ?> Votes
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-md-end">
                                        <?php
                                            $hasVoted = auth()->user()->votes()->where('election_id', $election->id)->exists();
                                        ?>
                                        <?php if($hasVoted): ?>
                                            <span class="badge bg-info mb-2">✓ You have voted</span>
                                            <a href="<?php echo e(route('voting.results', $election)); ?>" class="btn btn-sm btn-outline-primary d-block">
                                                <i class="bi bi-eye"></i> View Results
                                            </a>
                                        <?php else: ?>
                                            <?php if(!auth()->user()->verified_at): ?>
                                                <button class="btn btn-sm btn-secondary w-100" disabled>
                                                    <i class="bi bi-lock"></i> Pending Verification
                                                </button>
                                            <?php else: ?>
                                                <a href="<?php echo e(route('voting.show', $election)); ?>" class="btn btn-sm btn-primary w-100">
                                                    <i class="bi bi-hand-index"></i> Vote Now
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p class="mt-3 mb-0">No active elections at this time.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Upcoming Elections -->
            <div class="card border-0 shadow mt-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-calendar-event"></i> Upcoming Elections</h5>
                </div>
                <div class="card-body p-0">
                    <?php
                        $upcoming = ($elections ?? [])->where('status', 'pending');
                    ?>
                    <?php $__empty_1 = true; $__currentLoopData = $upcoming; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $election): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="p-4 border-bottom">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h6 class="mb-1 fw-bold"><?php echo e($election->title); ?></h6>
                                    <p class="text-muted small mb-0"><?php echo e($election->description); ?></p>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-hourglass-split"></i> Coming Soon
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="p-4 text-center text-muted">
                            <p class="mb-0">No upcoming elections scheduled.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card border-0 shadow mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-lightning-fill"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <a href="<?php echo e(route('voting.index')); ?>" class="btn btn-outline-primary w-100 mb-2">
                        <i class="bi bi-ballot"></i> View All Elections
                    </a>
                    <a href="#" class="btn btn-outline-secondary w-100" data-bs-toggle="modal" data-bs-target="#accountModal">
                        <i class="bi bi-person-gear"></i> Account Settings
                    </a>
                </div>
            </div>

            <!-- Account Information -->
            <div class="card border-0 shadow mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-person-circle"></i> Account Info</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-6 text-muted small">Name:</dt>
                        <dd class="col-sm-6 fw-bold"><?php echo e(auth()->user()->name); ?></dd>

                        <dt class="col-sm-6 text-muted small">Email:</dt>
                        <dd class="col-sm-6 fw-bold text-break"><?php echo e(auth()->user()->email); ?></dd>

                        <dt class="col-sm-6 text-muted small">Status:</dt>
                        <dd class="col-sm-6">
                            <?php if(auth()->user()->verified_at): ?>
                                <span class="badge bg-success"><i class="bi bi-check"></i> Verified</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Pending</span>
                            <?php endif; ?>
                        </dd>

                        <dt class="col-sm-6 text-muted small">Member Since:</dt>
                        <dd class="col-sm-6 small"><?php echo e(auth()->user()->created_at->format('M d, Y')); ?></dd>
                    </dl>
                </div>
            </div>

            <!-- Security Tips -->
            <div class="card border-0 shadow border-left-primary" style="border-left: 4px solid #0d6efd;">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-shield-check text-primary"></i> Security Tips</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled small text-muted">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i> 
                            Always use a strong password
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i> 
                            Never share your login credentials
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i> 
                            Verify the website URL before logging in
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i> 
                            Your votes are encrypted and anonymous
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-check-circle text-success"></i> 
                            Review your voting history in audit logs
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Account Settings Modal -->
<div class="modal fade" id="accountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Account Settings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Additional account settings and options would appear here.</p>
                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="bi bi-key"></i> Change Password
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="bi bi-file-text"></i> View Audit Logs
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="bi bi-shield"></i> Privacy Settings
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Secure-Online-Voting-System-Laravel-\resources\views/pages/home.blade.php ENDPATH**/ ?>