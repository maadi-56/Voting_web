<?php
include_once 'config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $voter_name = mysqli_real_escape_string($conn, $_POST['voter_name']);
    $voter_id = trim($_POST['voter_id']); 
    $plain_password = $_POST['password']; 

    // 1. BACKEND VALIDATION MATRIX
    if (!ctype_digit($voter_id) || strlen($voter_id) !== 13) {
        $error = "CNIC must be exactly 13 digits and contain numbers only.";
    } elseif (strlen($plain_password) !== 8) {
        $error = "Password token must be exactly 8 characters long.";
    } else {
        $voter_id = mysqli_real_escape_string($conn, $voter_id);
        $password = mysqli_real_escape_string($conn, $plain_password);

        if (isset($_FILES['voter_img']) && $_FILES['voter_img']['error'] == 0) {
            $tmp_name = $_FILES['voter_img']['tmp_name'];
            $file_type = $_FILES['voter_img']['type'];

            $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
            if (in_array($file_type, $allowed_types)) {
                
                $img_binary_data = file_get_contents($tmp_name);
                
                // --- FIX: Dynamically raise database transmission packet size limits to 16MB ---
                mysqli_query($conn, "SET GLOBAL max_allowed_packet=16777216;");
                
                $check_query = "SELECT * FROM voters WHERE cnic = ?";
                $stmt = mysqli_prepare($conn, $check_query);
                
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "s", $voter_id);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);

                    if (mysqli_num_rows($result) > 0) {
                        $error = "This CNIC is already registered under a secure token.";
                    } else {
                        $insert_query = "INSERT INTO voters (fullname, password, cnic, image_url) VALUES (?, ?, ?, ?)";
                        $insert_stmt = mysqli_prepare($conn, $insert_query);
                        
                        if ($insert_stmt) {
                            $null_param = NULL; 
                            
                            mysqli_stmt_bind_param($insert_stmt, "sssb", $voter_name, $password, $voter_id, $null_param);
                            mysqli_stmt_send_long_data($insert_stmt, 3, $img_binary_data);

                            if (mysqli_stmt_execute($insert_stmt)) {
                                $success = "Profile generated successfully! You can now log in.";
                            } else {
                                $error = "Execution failure: " . mysqli_stmt_error($insert_stmt);
                            }
                            mysqli_stmt_close($insert_stmt);
                        } else {
                            $error = "Insert Statement Preparation Failed: " . mysqli_error($conn);
                        }
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $error = "Check Statement Preparation Failed: " . mysqli_error($conn);
                }
            } else {
                $error = "Invalid file matrix. Only JPEG, PNG, and WEBP formats are accepted.";
            }
        } else {
            $error = "Biometric credential profile image is required.";
        }
    }
}
?>

<?php include_once 'includes/header.php'; ?>

<main class="max-w-md mx-auto my-12 p-4">
    <div class="bg-white/80 backdrop-blur-xl border border-slate-200/80 rounded-[32px] p-8 shadow-xl relative overflow-hidden">
        <div class="absolute -right-16 -top-16 w-40 h-40 bg-emerald-500/5 rounded-full blur-2xl pointer-events-none"></div>
        
        <div class="text-center space-y-2 mb-8">
            <span class="text-xs font-black text-emerald-700 bg-emerald-50 border border-emerald-200/60 px-3 py-1 rounded-full uppercase tracking-wider">Secure Node Entry</span>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Citizen Registration</h2>
            <p class="text-xs text-slate-400 font-medium">Create your unique profile token.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 text-xs font-semibold p-4 rounded-2xl mb-6 flex items-center gap-2 break-words">
                <span>⚠️</span> <span class="flex-1"><?php echo $error; ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold p-4 rounded-2xl mb-6 flex items-center gap-2">
                <span>✅</span> <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <form action="signup.php" method="POST" enctype="multipart/form-data" class="space-y-5">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Full Legal Name</label>
                <input type="text" name="voter_name" required placeholder="e.g., Muhammad Ali" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm font-medium px-4 py-3 rounded-xl focus:outline-none focus:border-emerald-500 transition shadow-inner">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Voter Identification ID (CNIC)</label>
                <input type="text" name="voter_id" required inputmode="numeric" pattern="[0-9]{13}" maxlength="13" placeholder="e.g., 4210112345673" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm font-medium px-4 py-3 rounded-xl focus:outline-none focus:border-emerald-500 transition shadow-inner">
                <span class="text-[10px] text-slate-400 mt-1 block">Must be exactly 13 numeric digits without dashes.</span>
            </div>

         <div>
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Secure Access Key (Password)</label>
            <!-- Relative wrapper elements frame structural placement positions for internal layout icons -->
            <div class="relative">
                <input type="password" id="passwordField" name="password" required minlength="8" maxlength="8" placeholder="Exactly 8 characters" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm font-medium pl-4 pr-12 py-3 rounded-xl focus:outline-none focus:border-emerald-500 transition shadow-inner">
                
                <button type="button" id="togglePasswordBtn" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 transition-colors focus:outline-none">
                    <!-- Eye Icon (Visible by Default when input is masked) -->
                    <svg id="eyeIconOpen" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <!-- Crossed Eye Icon (Hidden by Default when input is masked) -->
                    <svg id="eyeIconClosed" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.025 10.025 0 014.132-5.4M9.9 4.24a9.122 9.122 0 012.1-.24c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.4M3 3l18 18" />
                    </svg>
                </button>
            </div>
            <span class="text-[10px] text-slate-400 mt-1 block">Must be exactly 8 characters long.</span>
        </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Biometric Profile Image</label>
                <div class="relative w-full bg-slate-50 border border-dashed border-slate-300 rounded-xl p-4 text-center hover:border-emerald-500 transition cursor-pointer group">
                    <input type="file" name="voter_img" required accept="image/*" class="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10" id="fileInput">
                    <div class="space-y-1">
                        <span class="text-xl block group-hover:scale-110 transition duration-300">📸</span>
                        <span class="text-xs font-bold text-slate-600 block" id="fileNameDisplay">Upload Digital Credential</span>
                        <span class="text-[10px] text-slate-400 font-medium block">JPG, PNG, or WEBP maps directly to database storage</span>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold py-4 rounded-xl transition shadow-md tracking-wide uppercase mt-2 active:scale-[0.99]">
                Generate System Credentials
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-xs text-slate-400 font-medium">Already registered? <a href="login.php" class="text-blue-600 font-bold hover:underline">Sign In Instead</a></p>
        </div>
    </div>
</main>

<script>
// Real-time layout context switching for registration password fields
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







    document.getElementById('fileInput').addEventListener('change', function(e) {
        const fileName = e.target.files[0] ? e.target.files[0].name : "Upload Digital Credential";
        const display = document.getElementById('fileNameDisplay');
        display.innerText = fileName;
        display.classList.add('text-emerald-600');
    });

    document.getElementsByName('voter_id')[0].addEventListener('input', function (e) {
        this.value = this.value.replace(/[^0-9]/g, ''); 
    });
</script>

<?php include_once 'includes/footer.php'; ?>