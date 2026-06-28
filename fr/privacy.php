<?php 
include_once 'config.php'; 

// Start session if not already running
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['voter_id'])) {
    header("Location: login.php");
    exit;
}

?>
<?php include_once 'includes/header.php'; ?>

<!-- Styling overrides for the premium dark green hero and corporate aesthetic -->
<style>
    .privacy-dark-hero {
        background: linear-gradient(135deg, #064e3b 0%, #022c22 100%);
        border: 1px solid #065f46;
    }
    /* Premium dark-white / warm wheat card styling */
    .policy-panel {
        background: rgba(245, 245, 244, 0.95); /* Warm slate/stone hue */
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(214, 211, 209, 0.7);
    }
    /* Smooth behavior when snapping via the sidebar anchors */
    html {
        scroll-behavior: smooth;
    }
</style>

<main class="max-w-6xl mx-auto p-4 md:p-6 space-y-10 mt-4">
    
    <!-- PREMIUM DARK GREEN HERO SECTION -->
    <section class="privacy-dark-hero rounded-3xl p-8 md:p-12 text-center relative overflow-hidden shadow-lg">
        <!-- Subtle corporate grid pattern overlay -->
        <div class="absolute inset-0 pointer-events-none opacity-[0.03] bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
        
        <span class="bg-white/10 text-emerald-300 border border-emerald-500/30 text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">
            Data Governance & Security
        </span>
        
        <h1 class="text-3xl md:text-5xl font-black text-white tracking-tight mt-4 max-w-2xl mx-auto">
            Privacy & Ledger Integrity Protocols
        </h1>
        
        <p class="text-emerald-100/80 text-sm md:text-base mt-4 max-w-xl mx-auto font-medium leading-relaxed">
            Your trust is fundamental to democratic transparency. Discover exactly how our application architecture structures, validates, and isolates voter identity from the casting process.
        </p>
    </section>

    <!-- CORE PRIVACY POLICIES CONTAINER -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-8 items-start">
        
        <!-- Sticky Sub-Navigation Directory with Click Indicators -->
        <div class="md:col-span-1 space-y-2 md:sticky md:top-24 bg-white/60 p-3 rounded-2xl border border-slate-200/60 shadow-sm" id="policy-menu">
            <p class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 px-3 mb-2">Policy Directory</p>
            
            <a href="#voter-registration" onclick="setActiveLink(this)" class="menu-link block w-full text-left px-4 py-2.5 rounded-xl text-xs font-bold bg-slate-900 text-white shadow-sm transition-all duration-200">
                1. Account Creation Telemetry
            </a>
            <a href="#ballot-isolation" onclick="setActiveLink(this)" class="menu-link block w-full text-left px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-900 hover:bg-slate-100/80 transition-all duration-200">
                2. Ballot Anonymization
            </a>
            <a href="#data-retention" onclick="setActiveLink(this)" class="menu-link block w-full text-left px-4 py-2.5 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-900 hover:bg-slate-100/80 transition-all duration-200">
                3. Lifecycle & Retention
            </a>
        </div>

        <!-- Policy Explanations Sandbox (Wheat / Dark White background panels) -->
        <div class="md:col-span-2 space-y-6">
            
            <!-- Section 1: Signup & Login Telemetry -->
            <div id="voter-registration" class="policy-panel p-6 md:p-8 rounded-3xl shadow-sm space-y-4 scroll-mt-28">
                <div class="flex items-center gap-2">
                    <span class="text-lg">🔑</span>
                    <h2 class="text-base font-black text-slate-900 tracking-tight">Information Gathered During Signup & Login</h2>
                </div>
                
                <p class="text-slate-600 text-xs font-medium leading-relaxed">
                    To maintain strict democratic parity and enforce a strict "one citizen, one vote" runtime rule, our system requires unique parameters during registration profiles.
                </p>

                <!-- Clean Structured Data Table -->
                <div class="border border-stone-200 rounded-xl overflow-hidden text-xs bg-white/90">
                    <div class="grid grid-cols-3 bg-stone-100/80 border-b border-stone-200 p-3 font-bold text-stone-500 uppercase tracking-wider text-[10px]">
                        <div>Data Scope</div>
                        <div class="col-span-2">Technical Purpose & Processing Rule</div>
                    </div>
                    <div class="grid grid-cols-3 p-3 border-b border-stone-100 font-medium text-slate-700">
                        <div class="font-bold text-slate-900">National Identity Number (CNIC)</div>
                        <div class="col-span-2 text-slate-500">Salted and cryptographically cross-matched to prevent double registration. It is never stored in plain text.</div>
                    </div>
                    <div class="grid grid-cols-3 p-3 border-b border-stone-100 font-medium text-slate-700">
                        <div class="font-bold text-slate-900">Mobile Phone / Gateway Address</div>
                        <div class="col-span-2 text-slate-500">Utilized solely for active OTP verification tasks during high-traffic ballot submission loops.</div>
                    </div>
                    <div class="grid grid-cols-3 p-3 font-medium text-slate-700">
                        <div class="font-bold text-slate-900">Authentication Password Hash</div>
                        <div class="col-span-2 text-slate-500">Processed using default secure PHP hashing algorithms to keep portal login environments protected.</div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Ballot Isolation -->
            <div id="ballot-isolation" class="policy-panel p-6 md:p-8 rounded-3xl shadow-sm space-y-4 scroll-mt-28">
                <div class="flex items-center gap-2">
                    <span class="text-lg">🛡️</span>
                    <h2 class="text-base font-black text-slate-900 tracking-tight">The Ballot Anonymization Architecture</h2>
                </div>
                <p class="text-slate-600 text-xs font-medium leading-relaxed">
                    Once a voter logs in and submits a ballot selection for either the <strong>Punjab Level</strong> or <strong>District Level</strong> race, the platform uses an isolated ledger design pattern. Your authenticated session identifier is checked to confirm eligibility, but your specific selection is unlinked from your account profile—ensuring complete secret ballot protection.
                </p>
            </div>

            <!-- Section 3: Lifespans -->
            <div id="data-retention" class="policy-panel p-6 md:p-8 rounded-3xl shadow-sm space-y-4 scroll-mt-28">
                <div class="flex items-center gap-2">
                    <span class="text-lg">⏳</span>
                    <h2 class="text-base font-black text-slate-900 tracking-tight">Session Lifespans & Data Retention</h2>
                </div>
                <p class="text-slate-600 text-xs font-medium leading-relaxed">
                    Active verification sessions expire automatically after a predefined period of inactivity. When the official election tracking cycle closes, auxiliary network station logs and IP metadata records are wiped automatically to maintain a clean system perimeter.
                </p>
            </div>

        </div>
    </section>

</main>

<!-- Vanilla JavaScript Handler for Menu Navigation Active Toggles -->
<script>
    function setActiveLink(clickedElement) {
        // Get all links matching our navigation class
        const links = document.querySelectorAll('.menu-link');
        
        // Strip active slate styles from all options and return to muted text styling
        links.forEach(link => {
            link.classList.remove('bg-slate-900', 'text-white', 'shadow-sm', 'font-bold');
            link.classList.add('text-slate-500', 'font-semibold', 'hover:text-slate-900', 'hover:bg-slate-100/80');
        });
        
        // Apply pristine active styling rules instantly to chosen link
        clickedElement.classList.add('bg-slate-900', 'text-white', 'shadow-sm', 'font-bold');
        clickedElement.classList.remove('text-slate-500', 'font-semibold', 'hover:text-slate-900', 'hover:bg-slate-100/80');
    }

    // Optional dynamic scroll spy tracker: Highlights navbar links dynamically while reading down the dashboard
    window.addEventListener('scroll', () => {
        const sections = document.querySelectorAll('.policy-panel');
        const links = document.querySelectorAll('.menu-link');
        
        let currentSectionId = "";
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            if (pageYOffset >= sectionTop - 140) {
                currentSectionId = section.getAttribute('id');
            }
        });

        if(currentSectionId) {
            links.forEach(link => {
                if(link.getAttribute('href') === `#${currentSectionId}`) {
                    link.classList.add('bg-slate-900', 'text-white', 'shadow-sm', 'font-bold');
                    link.classList.remove('text-slate-500', 'font-semibold', 'hover:text-slate-900', 'hover:bg-slate-100/80');
                } else {
                    link.classList.remove('bg-slate-900', 'text-white', 'shadow-sm', 'font-bold');
                    link.classList.add('text-slate-500', 'font-semibold', 'hover:text-slate-900', 'hover:bg-slate-100/80');
                }
            });
        }
    });
</script>

<?php include_once 'includes/footer.php'; ?>