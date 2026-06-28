<?php
include_once 'config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cnic = trim($_POST['cnic']);
    $password = trim($_POST['password']);
    
    // 1. BACKEND STRUCTURE INTEGRITY CHECK
    if (!ctype_digit($cnic) || strlen($cnic) !== 13) {
        $error = "Invalid CNIC or password configuration criteria.";
    } elseif (strlen($password) !== 8) {
        $error = "Invalid CNIC or password configuration criteria.";
    } else {
        $cnic = mysqli_real_escape_string($conn, $cnic);
        
        // --- STEP A: CHECK FOR ADMIN AUTHENTICATION FIRST ---
        // Assuming your administrators have a 'role' column or exist in a dedicated table.
        // Option 1: If admins are inside the 'voters' table but flagged with a 'role' column:
        // $query = "SELECT * FROM voters WHERE cnic = ? AND role = 'admin'";
        
        // Option 2: If admins are stored in a unique dedicated table (e.g., 'admins'):
        $admin_query = "SELECT * FROM admins WHERE cnic = ?"; 
        $stmt = mysqli_prepare($conn, $admin_query);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $cnic);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) > 0) {
                $admin = mysqli_fetch_assoc($result);
                
                if ($password === $admin['password']) {
                    // Set the explicit session attributes your dashboard looks for
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_name'] = $admin['fullname'] ?? $admin['username'] ?? 'System Admin';
                    $_SESSION['admin_img'] = $admin['image_url'] ?? 'https://i.pravatar.cc/100';
                    
                    mysqli_stmt_close($stmt);
                    
                    // Route upstream outside the frontend folder into the dashboard panel
                    header("Location: ../voting-dashboard/dashboard.php");
                    exit;
                }
            }
            mysqli_stmt_close($stmt);
        }

        // --- STEP B: FALLBACK TO STANDARD VOTER AUTHENTICATION ---
        $query = "SELECT * FROM voters WHERE cnic = ?";
        $stmt = mysqli_prepare($conn, $query);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $cnic);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) > 0) {
                $voter = mysqli_fetch_assoc($result);
                    
                if ($password === $voter['password']) {
                    $_SESSION['voter_id'] = $voter['id'];
                    $_SESSION['voter_name'] = $voter['fullname'];
                    $_SESSION['voter_img'] = $voter['image_url'];
                    
                    mysqli_stmt_close($stmt);
                    
                    header("Location: index.php");
                    exit;
                } else {
                    $error = "Invalid CNIC or password configuration criteria.";
                }
            } else {
                $error = "Invalid CNIC or password configuration criteria.";
            }
            if(!empty($stmt)) { mysqli_stmt_close($stmt); }
        } else {
            $error = "Authentication subsystem failure.";
        }
    }
}
?>
<?php include_once 'includes/header.php'; ?>

<main class="max-w-md mx-auto mt-16 p-8 bg-white border border-slate-200 rounded-3xl shadow-xl hover:shadow-2xl transition-shadow duration-300">
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Voter Authentication Hub</h2>
        <p class="text-sm text-slate-500 mt-1">Authenticate to access your designated voter platform.</p>
    </div>

    <?php if(!empty($error)): ?>
        <div class="bg-red-50 text-red-600 text-xs font-medium p-4 rounded-xl border border-red-100 mb-4 animate-pulse">⚠️ <?php echo $error; ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST" class="space-y-5">
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">CNIC Number</label>
            <input type="text" name="cnic" required inputmode="numeric" pattern="[0-9]{13}" maxlength="13" placeholder="e.g., 4210112345673" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-mono focus:outline-none focus:border-emerald-500 focus:bg-white transition-all duration-200">
            <span class="text-[10px] text-slate-400 mt-1 block">Exactly 13 digits without dashes.</span>
        </div>
        
        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Password</label>
            <!-- Layout relative container to frame structural placement position for eye icons -->
            <div class="relative">
                <input type="password" id="passwordField" name="password" required minlength="8" maxlength="8" placeholder="••••••••" class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-4 pr-12 py-3 text-sm focus:outline-none focus:border-emerald-500 focus:bg-white transition-all duration-200">
                
                <button type="button" id="togglePasswordBtn" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 transition-colors focus:outline-none">
                    <!-- Eye Icon (Visible by Default when hidden) -->
                    <svg id="eyeIconOpen" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <!-- Crossed Eye Icon (Hidden by Default) -->
                    <svg id="eyeIconClosed" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.025 10.025 0 014.132-5.4M9.9 4.24a9.122 9.122 0 012.1-.24c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.4M3 3l18 18" />
                    </svg>
                </button>
            </div>
            <span class="text-[10px] text-slate-400 mt-1 block">Exactly 8 characters long.</span>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-xl text-sm font-semibold tracking-wide transition-all duration-200 transform active:scale-[0.98] shadow-md hover:shadow-lg">
                Unlock Ballot Box
            </button>
        </div>
    </form>
</main>

<script>
    // Input filter to instantly wipe out characters that aren't numbers
    document.getElementsByName('cnic')[0].addEventListener('input', function (e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Real-time layout context switching for password visibility settings
    const passwordField = document.getElementById('passwordField');
    const togglePasswordBtn = document.getElementById('togglePasswordBtn');
    const eyeIconOpen = document.getElementById('eyeIconOpen');
    const eyeIconClosed = document.getElementById('eyeIconClosed');

    togglePasswordBtn.addEventListener('click', function() {
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            eyeIconOpen.classList.add('hidden');
            eyeIconClosed.classList.remove('hidden');
        } else {
            passwordField.type = 'password';
            eyeIconOpen.classList.remove('hidden');
            eyeIconClosed.classList.add('hidden');
        }
    });
</script>

<?php include_once 'includes/footer.php'; ?>