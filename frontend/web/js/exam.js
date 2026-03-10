/**
 * Pure Vanilla ES6 Module managing the Exam State Machine
 */

class Timer {
    constructor(seconds, elementId, onFinishCallback) {
        this.remaining = seconds;
        this.element = document.getElementById(elementId);
        this.onFinish = onFinishCallback;
        this.interval = null;
    }

    start() {
        this.updateDisplay();
        this.interval = setInterval(() => {
            this.remaining--;
            this.updateDisplay();

            if (this.remaining === 300) { // 5 minutes warning
                this.element.classList.add('timer-warning');
            }

            if (this.remaining <= 0) {
                this.stop();
                this.onFinish();
            }
        }, 1000);
    }

    stop() {
        clearInterval(this.interval);
    }

    updateDisplay() {
        let h = Math.floor(this.remaining / 3600);
        let m = Math.floor((this.remaining % 3600) / 60);
        let s = this.remaining % 60;
        
        h = h < 10 ? '0' + h : h;
        m = m < 10 ? '0' + m : m;
        s = s < 10 ? '0' + s : s;
        
        this.element.textContent = `${h}:${m}:${s}`;
    }
}

class NavigationManager {
    constructor() {
        this.buttons = document.querySelectorAll('.q-btn');
        this.cards = document.querySelectorAll('.question-card');
        this.currentIndex = 0;
        this.init();
    }

    init() {
        this.buttons.forEach((btn, index) => {
            btn.addEventListener('click', () => this.goTo(index));
        });

        document.querySelectorAll('.btn-prev').forEach((btn, index) => {
            btn.addEventListener('click', () => this.goTo(index - 1));
        });

        document.querySelectorAll('.btn-next').forEach((btn, index) => {
            btn.addEventListener('click', () => this.goTo(index + 1));
        });
    }

    goTo(index) {
        if (index < 0 || index >= this.cards.length) return;
        
        // Update UI Maps
        this.buttons[this.currentIndex].classList.remove('active');
        this.cards[this.currentIndex].classList.remove('active');
        
        this.currentIndex = index;
        
        this.buttons[this.currentIndex].classList.add('active');
        this.cards[this.currentIndex].classList.add('active');
    }
    
    markAnswered(questionId) {
        const btn = document.querySelector(`.q-btn[data-target="${questionId}"]`);
        if (btn) btn.classList.add('answered');
    }
}

class AnswerManager {
    constructor(navManager) {
        this.nav = navManager;
        this.init();
    }

    init() {
        document.querySelectorAll('.option-card').forEach(card => {
            card.addEventListener('click', (e) => this.selectOption(e.currentTarget));
        });
    }

    selectOption(card) {
        // Find parent container and remove active state from siblings
        const container = card.closest('.options-container');
        container.querySelectorAll('.option-card').forEach(c => c.classList.remove('selected'));
        
        // Mark current as selected
        card.classList.add('selected');
        
        const qId = card.getAttribute('data-q-id');
        const optId = card.getAttribute('data-opt-id');
        const examId = card.getAttribute('data-exam-id');
        
        // Update Nav visual
        this.nav.markAnswered(qId);
        
        // Save via AJAX in background asynchronously to prevent UX blocking
        this.syncToServer(examId, qId, optId);
    }

    syncToServer(examId, questionId, optionId) {
        // Grab CSRF from meta tags configured by Yii2 layout natively
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch('/api/exam/answer', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken
            },
            body: JSON.stringify({
                studentExamId: examId,
                questionId: questionId,
                optionId: optionId
            })
        }).catch(err => console.error('Failed to sync answer:', err));
    }
}

class ExamSession {
    constructor() {
        this.nav = new NavigationManager();
        this.answers = new AnswerManager(this.nav);
        this.cheatAttempts = 0;
        
        // Extract time payload rendered into DOM securely by PHP earlier
        const timerEl = document.getElementById('timer');
        const seconds = parseInt(timerEl.getAttribute('data-seconds'), 10);
        
        this.timer = new Timer(seconds, 'timer', () => {
            this.forceSubmit();
        });
        
        this.initAntiCheat();
        this.timer.start();
    }
    
    initAntiCheat() {
        // 1. Prevent Right Click & Text Highlighting
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('selectstart', e => e.preventDefault());
        document.addEventListener('copy', e => {
            e.preventDefault();
            alert("Nusxa ko'chirish taqiqlanadi!");
        });

        // 2. Tab switching monitor
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'hidden') {
                this.cheatAttempts++;
                document.getElementById('cheat-overlay').style.display = 'flex';
                
                // If they cheat more than 3 times, auto submit
                if (this.cheatAttempts >= 3) {
                    this.forceSubmit();
                }
            }
        });
        
        // Dismiss overlay
        document.getElementById('resume-btn').addEventListener('click', () => {
            document.getElementById('cheat-overlay').style.display = 'none';
        });

        // 3. Confirm exit organically, but handle browser limits
        window.addEventListener('beforeunload', (e) => {
            // Browsers strictly limit custom messages now, standard warning applies
            e.preventDefault(); 
            e.returnValue = ''; 
        });
    }

    forceSubmit() {
        // Triggers the logical PHP end route mapped to the button
        window.onbeforeunload = null; // Remove blocking listener
        document.getElementById('btn-force-finish').click();
    }
}

// Bootstrap
document.addEventListener('DOMContentLoaded', () => {
    window.ExamApp = new ExamSession();
});
