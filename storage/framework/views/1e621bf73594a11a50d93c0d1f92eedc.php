

<?php $__env->startSection('title', 'Admin Dashboard'); ?>

<?php
    use Illuminate\Support\Str;
?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <h2 class="mb-4"><i class="bi bi-speedometer2"></i> Admin Dashboard</h2>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Elections</h5>
                    <h2><?php echo e($totalElections); ?></h2>
                    <small><?php echo e($activeElections); ?> Active</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Votes</h5>
                    <h2><?php echo e($totalVotes); ?></h2>
                    <small>All verified votes</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Registered Users</h5>
                    <h2><?php echo e($totalUsers); ?></h2>
                    <small><?php echo e($pendingVerifications); ?> Pending</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Pending Verifications</h5>
                    <h2><?php echo e($pendingVerifications); ?></h2>
                    <a href="<?php echo e(route('admin.users')); ?>" class="text-white">View Users →</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <a href="<?php echo e(route('admin.elections.create')); ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Create New Election
                    </a>
                    <a href="<?php echo e(route('admin.users')); ?>" class="btn btn-info">
                        <i class="bi bi-people"></i> Manage Users
                    </a>
                    <a href="<?php echo e(route('admin.logs')); ?>" class="btn btn-secondary">
                        <i class="bi bi-journal-text"></i> View Logs
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Elections -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Elections</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Votes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $recentElections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $election): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($election->title); ?></td>
                                        <td>
                                            <?php if($election->isActive()): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php elseif($election->hasEnded()): ?>
                                                <span class="badge bg-secondary">Ended</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Upcoming</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($election->votes->count()); ?></td>
                                        <td>
                                            <a href="<?php echo e(route('admin.elections.edit', $election)); ?>" class="btn btn-sm btn-primary">Edit</a>
                                            <a href="<?php echo e(route('admin.elections.results', $election)); ?>" class="btn btn-sm btn-info">Results</a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Votes -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Votes</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Election</th>
                                    <th>Voter</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $recentVotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e(Str::limit($vote->election->title, 20)); ?></td>
                                        <td><?php echo e($vote->user?->voter_id ?? 'Anonymous'); ?></td>
                                        <td><?php echo e($vote->created_at?->diffForHumans()); ?></td>
                                        <td>
                                            <?php if($vote->is_tampered): ?>
                                                <span class="badge bg-danger">Tampered</span>
                                            <?php elseif($vote->is_verified): ?>
                                                <span class="badge bg-success">Verified</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Secure-Online-Voting-System-Laravel-\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>