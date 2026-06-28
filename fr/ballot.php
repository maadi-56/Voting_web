<?php
include_once 'config.php';
if (session_status() == PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['voter_id'])) {
    header("Location: login.php");
    exit;
}

$voter_id = intval($_SESSION['voter_id']);
$message = '';

// Create the log table automatically if it doesn't exist yet
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `voter_votes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `voter_id` INT NOT NULL,
  `election_level` VARCHAR(100) NOT NULL,
  `candidate_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_voter_level` (`voter_id`, `election_level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Process incremental database updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['candidate_id']) && isset($_POST['election_level'])) {
    $candidate_id = intval($_POST['candidate_id']);
    $election_level = mysqli_real_escape_string($conn, $_POST['election_level']);
    
    // Check if voter has already submitted a ballot for this tier level
    $check_query = mysqli_query($conn, "SELECT id FROM voter_votes WHERE voter_id = $voter_id AND election_level = '$election_level'");
    
    if (mysqli_num_rows($check_query) == 0) {
        mysqli_query($conn, "START TRANSACTION");
        
        $vote_query = "UPDATE candidates SET votes = votes + 1 WHERE id = $candidate_id";
        $log_query = "INSERT INTO voter_votes (voter_id, election_level, candidate_id) VALUES ($voter_id, '$election_level', $candidate_id)";
        
        if (mysqli_query($conn, $vote_query) && mysqli_query($conn, $log_query)) {
            mysqli_query($conn, "COMMIT");
            $message = "<div class='bg-emerald-50 text-emerald-700 p-4 rounded-2xl border border-emerald-200/80 mb-6 font-semibold text-xs tracking-wide shadow-sm animate-fade-in flex items-center gap-2'><span>🎉</span> Your ballot allocation submission was verified and logged successfully!</div>";
        } else {
            mysqli_query($conn, "ROLLBACK");
            $message = "<div class='bg-red-50 text-red-600 p-4 rounded-2xl border border-red-200 mb-6 font-medium text-xs shadow-sm'>⚠️ Structural transmission error: " . mysqli_error($conn) . "</div>";
        }
    } else {
        $message = "<div class='bg-amber-50 text-amber-700 p-4 rounded-2xl border border-amber-200/80 mb-6 font-medium text-xs shadow-sm flex items-center gap-2'><span>⚠️</span> Access Denied: You have already assigned your vote for this level tier.</div>";
    }
}

// Map existing database logs out to array tokens to handle frontend conditional locks
$voted_levels = [];
$voted_candidates = [];
$history_query = mysqli_query($conn, "SELECT election_level, candidate_id FROM voter_votes WHERE voter_id = $voter_id");
if ($history_query) {
    while ($h_row = mysqli_fetch_assoc($history_query)) {
        $voted_levels[] = $h_row['election_level'];
        $voted_candidates[] = $h_row['candidate_id'];
    }
}

// Fetch candidate choices
$query = "SELECT * FROM candidates ORDER BY election_level ASC, id ASC";
$result = mysqli_query($conn, $query);
?>
<?php include_once 'includes/header.php'; ?>

<!-- Premium Light Decent Style Layers -->
<style>
    body {
        background: radial-gradient(circle at 50% 0%, #f8fafc 0%, #f1f5f9 100%);
        min-height: 100vh;
    }
    .ballot-dark-hero {
        background: linear-gradient(135deg, #064e3b 0%, #022c22 100%);
        border: 1px solid #065f46;
    }
    .candidate-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .candidate-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 40px rgba(148, 163, 184, 0.08);
    }
    /* Balanced Focus Interaction Grid */
    .ballot-matrix:hover .candidate-card:not(:hover) {
        opacity: 0.75;
    }
</style>

<main class="max-w-5xl mx-auto p-4 md:p-6 space-y-8 mt-4">

    <!-- PREMIUM DARK GREEN HERO SECTION -->
    <section class="ballot-dark-hero rounded-3xl p-8 md:p-10 text-center relative overflow-hidden shadow-lg">
        <div class="absolute inset-0 pointer-events-none opacity-[0.03] bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
        
        <span class="inline-flex items-center gap-1.5 text-[10px] font-extrabold tracking-wider text-emerald-300 bg-white/10 border border-emerald-500/30 px-3 py-1 rounded-full uppercase mb-3">
            <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span> Live Gateway Secured
        </span>
        
        <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight mt-1">
            Secure Digital Ballot Entry
        </h1>
        
        <p class="text-emerald-100/80 text-xs md:text-sm mt-3 max-w-xl mx-auto font-medium leading-relaxed">
            Authorized voter node active. Please review candidate profile credentials thoroughly before casting permanent tokens into the ledger matrix.
        </p>
    </section>

    <!-- Error/Success Transmissions -->
    <div class="max-w-4xl mx-auto">
        <?php echo $message; ?>
    </div>

    <!-- MAIN BALLOT INTERACTIVE GRID -->
    <div class="ballot-matrix grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl mx-auto">
        <?php if($result && mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): 
                $level = $row['election_level'];
                $c_id = $row['id'];
                
                // Determine layout rules based on historical vote log matches
                $is_this_level_voted = in_array($level, $voted_levels);
                $is_this_candidate_voted = in_array($c_id, $voted_candidates);
            ?>
                <div class="candidate-card border rounded-3xl p-6 flex flex-col justify-between <?php echo $is_this_candidate_voted ? 'border-emerald-500 ring-4 ring-emerald-500/5 bg-white shadow-sm' : 'border-slate-200/80 shadow-xs'; ?>">
                    
                    <div class="flex items-start space-x-4">
                        <!-- Candidate Picture Container -->
                        <div class="relative overflow-hidden w-16 h-16 rounded-2xl border border-slate-200 shadow-inner bg-slate-50 flex-shrink-0">
                            <img src="<?php 
                                // FIXED: Convert binary stream directly from 'image' column to Base64
                                if (!empty($row['image'])) {
                                    echo 'data:image/jpeg;base64,' . base64_encode($row['image']); 
                                } else {
                                    echo 'uploads/card.webp'; 
                                }
                            ?>" 
                            class="w-full h-full object-cover" 
                            onerror="this.src='uploads/card.webp'">
                        </div>
                        
                        <div class="space-y-1 w-full">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-[9px] font-extrabold bg-slate-100 text-slate-600 px-2.5 py-0.5 rounded-md uppercase tracking-wider border border-slate-200/50">
                                    <?php echo htmlspecialchars($level); ?>
                                </span>
                                <?php if($is_this_candidate_voted): ?>
                                    <span class="text-[9px] font-extrabold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-md border border-emerald-200 uppercase tracking-wide">Selected</span>
                                <?php endif; ?>
                            </div>
                            
                            <h3 class="text-base font-black text-slate-900 pt-1 tracking-tight"><?php echo htmlspecialchars($row['name']); ?></h3>
                            <p class="text-[10px] font-extrabold text-blue-600 tracking-wider uppercase"><?php echo htmlspecialchars($row['party']); ?></p>
                            <p class="text-xs text-slate-500 leading-relaxed font-medium pt-1 line-clamp-2"><?php echo htmlspecialchars($row['description']); ?></p>
                        </div>
                    </div>

                    <!-- Submission Flow Panel -->
                    <form action="ballot.php" method="POST" class="mt-6 pt-4 border-t border-slate-100/80">
                        <input type="hidden" name="candidate_id" value="<?php echo $c_id; ?>">
                        <input type="hidden" name="election_level" value="<?php echo htmlspecialchars($level); ?>">
                        
                        <?php if (!$is_this_level_voted): ?>
                            <!-- Case A: Clean, Active Un-voted Action Button -->
                            <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold py-3 rounded-xl transition-all shadow-sm active:scale-[0.99]">
                                Cast Ballot Allocation
                            </button>
                        <?php elseif ($is_this_candidate_voted): ?>
                            <!-- Case B: The Chosen Active Success Button -->
                            <button type="button" disabled class="w-full bg-emerald-600 text-white text-xs font-bold py-3 rounded-xl shadow-md flex items-center justify-center gap-1.5 cursor-not-allowed">
                                <span>✓ Ballot Logged Successfully</span>
                            </button>
                        <?php else: ?>
                            <!-- Case C: The Bypassed / Locked Inactive Button -->
                            <button type="button" disabled class="w-full bg-slate-100 text-slate-400 text-xs font-bold py-3 rounded-xl border border-slate-200/60 cursor-not-allowed uppercase tracking-widest text-[9px]">
                                Tier Lock Active
                            </button>
                        <?php endif; ?>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="text-center py-12 text-slate-400 text-xs border border-dashed border-slate-200 rounded-3xl col-span-2 bg-white/50">No registered candidate configurations found.</div>
        <?php endif; ?>
    </div>
</main>

<?php include_once 'includes/footer.php'; ?>