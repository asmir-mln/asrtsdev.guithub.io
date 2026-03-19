/* AsArt'sDev | Signature invisible | ASmir Milia | ASARTSDEV_SIGNATURE_INVISIBLE */
/**
 * Vision du Futur - AsArt'sDev
 * Gère le popup d'accès microphone, la voix off et la présentation vidéo
 */

class VisionFutur {
    constructor() {
        this.microphoneGranted = false;
        this.speechSynth = window.speechSynthesis || null;
        this.init();
    }

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.attachVisionButton();
        });
    }

    attachVisionButton() {
        const btn = document.getElementById('btn-activer-vision');
        if (btn) {
            btn.addEventListener('click', () => this.lancerExperience());
        }
    }

    lancerExperience() {
        this.demanderMicrophone(() => {
            this.lancerVoixOff();
        });
    }

    /**
     * Affiche un popup futuriste pour demander l'accès au microphone.
     * Le callback est appelé une fois la permission accordée ou refusée.
     */
    demanderMicrophone(callback) {
        const overlay = document.createElement('div');
        overlay.id = 'mic-popup-overlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 5, 20, 0.92);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            animation: vfFadeIn 0.4s ease-in;
        `;

        const box = document.createElement('div');
        box.style.cssText = `
            background: linear-gradient(135deg, #0a0e27 0%, #0d1b2a 100%);
            border: 2px solid rgba(0, 243, 255, 0.5);
            box-shadow: 0 0 40px rgba(0, 243, 255, 0.3), inset 0 0 60px rgba(0, 0, 30, 0.8);
            padding: 40px 50px;
            border-radius: 16px;
            max-width: 480px;
            width: 90%;
            text-align: center;
            color: #e0f7ff;
            animation: vfSlideUp 0.5s ease-out;
        `;

        box.innerHTML = `
            <div style="font-size: 3rem; margin-bottom: 16px;" aria-hidden="true">🎙️</div>
            <h2 style="color: #00f3ff; margin: 0 0 12px; font-size: 1.4rem; letter-spacing: 1px;">
                Activer l'expérience sonore
            </h2>
            <p style="color: #9ecfdb; font-size: 0.95rem; line-height: 1.7; margin-bottom: 28px;">
                Ce site peut activer votre microphone pour offrir une expérience interactive.
                Vous pouvez refuser — la présentation continuera en mode lecture seule.
            </p>
            <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
                <button id="btn-mic-autoriser" style="
                    background: linear-gradient(135deg, #00f3ff, #0099ff);
                    color: #0a0e27;
                    border: none;
                    padding: 12px 28px;
                    border-radius: 8px;
                    cursor: pointer;
                    font-weight: 700;
                    font-size: 0.9rem;
                    letter-spacing: 0.5px;
                    transition: opacity 0.2s;
                ">✅ Autoriser le micro</button>
                <button id="btn-mic-refuser" style="
                    background: transparent;
                    color: #9ecfdb;
                    border: 1.5px solid rgba(0, 243, 255, 0.35);
                    padding: 12px 28px;
                    border-radius: 8px;
                    cursor: pointer;
                    font-size: 0.9rem;
                    transition: border-color 0.2s;
                ">Continuer sans micro</button>
            </div>
            <p id="mic-status-msg" style="margin-top: 20px; font-size: 0.8rem; color: #5ba3b0; min-height: 1.2em;"></p>
        `;

        overlay.appendChild(box);
        document.body.appendChild(overlay);

        const statusMsg = box.querySelector('#mic-status-msg');

        box.querySelector('#btn-mic-autoriser').addEventListener('click', () => {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                statusMsg.textContent = 'Microphone non disponible dans ce navigateur.';
                statusMsg.style.color = '#ff6b6b';
                setTimeout(() => {
                    overlay.remove();
                    callback();
                }, 1500);
                return;
            }
            statusMsg.textContent = 'Demande d\'accès en cours…';
            statusMsg.style.color = '#00f3ff';
            navigator.mediaDevices.getUserMedia({ audio: true })
                .then(stream => {
                    this.microphoneGranted = true;
                    stream.getTracks().forEach(track => track.stop());
                    statusMsg.textContent = '✓ Microphone activé !';
                    statusMsg.style.color = '#00e676';
                    setTimeout(() => {
                        overlay.remove();
                        callback();
                    }, 900);
                })
                .catch(() => {
                    statusMsg.textContent = 'Accès refusé — présentation en lecture seule.';
                    statusMsg.style.color = '#ffb74d';
                    setTimeout(() => {
                        overlay.remove();
                        callback();
                    }, 1500);
                });
        });

        box.querySelector('#btn-mic-refuser').addEventListener('click', () => {
            overlay.remove();
            callback();
        });

        overlay.addEventListener('click', e => {
            if (e.target === overlay) {
                overlay.remove();
                callback();
            }
        });
    }

    /**
     * Lance la voix off de la présentation via la Web Speech API.
     */
    lancerVoixOff() {
        const section = document.getElementById('vision-futur-section');
        if (section) {
            section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        if (!this.speechSynth) {
            this.afficherConclusion();
            return;
        }

        this.speechSynth.cancel();

        const texte = [
            "Ma vision repose sur l'équilibre entre les talents qui sont visibles très tôt et ceux qui restent longtemps cachés.",
            "Beaucoup de personnes, notamment celles qui sont dyslexiques ou qui ont un parcours différent, possèdent des capacités qui ne sont pas immédiatement détectées.",
            "Ce site est ma vision du futur.",
            "Un futur où la technologie accessible, l'intelligence artificielle éthique et la création humaine convergent pour un impact réel.",
        ].join(' ');

        const utterance = new SpeechSynthesisUtterance(texte);
        utterance.lang = 'fr-FR';
        utterance.rate = 0.92;
        utterance.pitch = 1.0;
        utterance.volume = 1.0;

        utterance.onstart = () => {
            const indicator = document.getElementById('voix-off-indicator');
            if (indicator) {
                indicator.style.display = 'flex';
            }
        };

        utterance.onend = () => {
            const indicator = document.getElementById('voix-off-indicator');
            if (indicator) {
                indicator.style.display = 'none';
            }
            this.afficherConclusion();
        };

        utterance.onerror = () => {
            const indicator = document.getElementById('voix-off-indicator');
            if (indicator) indicator.style.display = 'none';
            this.afficherConclusion();
        };

        this.speechSynth.speak(utterance);
    }

    /**
     * Met en valeur la phrase de conclusion.
     */
    afficherConclusion() {
        const conclusion = document.getElementById('vision-conclusion');
        if (!conclusion) return;
        conclusion.style.opacity = '0';
        conclusion.style.display = 'block';
        conclusion.style.transition = 'opacity 1.2s ease-in, transform 1.2s ease-out';
        conclusion.style.transform = 'translateY(20px)';
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                conclusion.style.opacity = '1';
                conclusion.style.transform = 'translateY(0)';
            });
        });
    }
}

// Injecter les keyframes CSS pour les animations du popup
(function injectVisionStyles() {
    if (document.querySelector('style[data-vision-futur]')) return;
    const style = document.createElement('style');
    style.setAttribute('data-vision-futur', 'true');
    style.textContent = `
        @keyframes vfFadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        @keyframes vfSlideUp {
            from { transform: translateY(40px); opacity: 0; }
            to   { transform: translateY(0);    opacity: 1; }
        }
        @keyframes vfPulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.6; transform: scale(1.08); }
        }
        @keyframes vfWave {
            0%   { transform: scaleY(0.4); }
            50%  { transform: scaleY(1.0); }
            100% { transform: scaleY(0.4); }
        }
    `;
    document.head.appendChild(style);
})();

// Initialisation
const visionFutur = new VisionFutur();
