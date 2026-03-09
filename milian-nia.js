/* AsArt'sDev | Signature invisible | ASmir Milia | ASARTSDEV_SIGNATURE_INVISIBLE */
/* 
 * Milian-NIA - Assistant IA Personnel
 * Intelligence Artificielle dédiée à AsArt'sDev
 * Cerveau neuronal avec néons bleus futuristes
 */

class MillianNIA {
  constructor() {
    this.isActive = false;
    this.conversationMode = 'contextuel';
    this.Init();
  }

  Init() {
    this.creerInterface();
    this.animerCerveau();
    this.ajouterEvenements();
  }

  creerInterface() {
    const niaContainer = document.createElement('div');
    niaContainer.id = 'milian-nia-container';
    niaContainer.innerHTML = `
      <div id="nia-widget" class="nia-minimized">
        <div class="nia-brain-icon">
          <svg viewBox="0 0 100 100" class="brain-svg">
            <!-- Cerveau stylisé -->
            <path d="M50 20 Q30 20 25 35 Q20 50 25 65 Q30 80 50 80 Q70 80 75 65 Q80 50 75 35 Q70 20 50 20" 
                  class="brain-outline" fill="none" stroke="url(#neonGradient)" stroke-width="2"/>
            <circle cx="40" cy="45" r="3" class="neuron neuron-1" fill="#00f3ff"/>
            <circle cx="60" cy="45" r="3" class="neuron neuron-2" fill="#00f3ff"/>
            <circle cx="50" cy="55" r="3" class="neuron neuron-3" fill="#0099ff"/>
            <circle cx="35" cy="60" r="2" class="neuron neuron-4" fill="#00d4ff"/>
            <circle cx="65" cy="60" r="2" class="neuron neuron-5" fill="#00d4ff"/>
            
            <!-- Synapses animées -->
            <line x1="40" y1="45" x2="50" y2="55" class="synapse synapse-1" stroke="#00f3ff" stroke-width="1" opacity="0.6"/>
            <line x1="60" y1="45" x2="50" y2="55" class="synapse synapse-2" stroke="#0099ff" stroke-width="1" opacity="0.6"/>
            <line x1="50" y1="55" x2="35" y2="60" class="synapse synapse-3" stroke="#00d4ff" stroke-width="1" opacity="0.6"/>
            <line x1="50" y1="55" x2="65" y2="60" class="synapse synapse-4" stroke="#00d4ff" stroke-width="1" opacity="0.6"/>
            
            <defs>
              <linearGradient id="neonGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" style="stop-color:#00f3ff;stop-opacity:1" />
                <stop offset="50%" style="stop-color:#0099ff;stop-opacity:1" />
                <stop offset="100%" style="stop-color:#0066ff;stop-opacity:1" />
              </linearGradient>
            </defs>
          </svg>
          <div class="nia-pulse"></div>
        </div>
        <div class="nia-name">Milian-NIA</div>
      </div>

      <div id="nia-panel" class="nia-hidden">
        <div class="nia-header">
          <h3>
            <span class="nia-icon">🧠</span>
            Milian-NIA
            <span class="nia-status">● En ligne</span>
          </h3>
          <button class="nia-close" onclick="milianNIA.fermer()">╳</button>
        </div>
        
        <div class="nia-content">
          <div class="nia-intro">
            <p>Bonjour, je suis <strong>Milian-NIA</strong>, votre assistant IA personnel.</p>
            <p class="nia-specs">
              💡 Intelligence adaptative | 🎨 Vision créative<br>
              🧩 Analyse dyslexique-HPI (QI 136) | 🚀 Innovation inclusive
            </p>
          </div>

          <div id="nia-messages" class="nia-messages">
            <!-- Messages IA apparaîtront ici -->
          </div>

          <div class="nia-controls">
            <button onclick="milianNIA.parlerContexte()" class="nia-btn">
              💬 Contexte de la page
            </button>
            <button onclick="milianNIA.expliquerVision()" class="nia-btn">
              🌟 Ma vision du monde
            </button>
          </div>
        </div>
      </div>
    `;

    document.body.appendChild(niaContainer);
    this.injecterStyles();
  }

  injecterStyles() {
    const style = document.createElement('style');
    style.textContent = `
      #milian-nia-container {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 99999;
        font-family: 'Segoe UI', system-ui, sans-serif;
      }

      #nia-widget {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #0a1929 0%, #1a2942 100%);
        border: 2px solid #00f3ff;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        box-shadow: 
          0 0 20px rgba(0, 243, 255, 0.5),
          0 0 40px rgba(0, 153, 255, 0.3),
          0 8px 32px rgba(0, 0, 0, 0.6);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
      }

      #nia-widget:hover {
        transform: scale(1.1) translateY(-5px);
        box-shadow: 
          0 0 30px rgba(0, 243, 255, 0.8),
          0 0 60px rgba(0, 153, 255, 0.5),
          0 12px 48px rgba(0, 0, 0, 0.8);
        border-color: #00d4ff;
      }

      #nia-widget::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(
          45deg,
          transparent,
          rgba(0, 243, 255, 0.1),
          transparent
        );
        transform: rotate(45deg);
        animation: niaShine 3s infinite;
      }

      @keyframes niaShine {
        0%, 100% { transform: rotate(45deg) translateY(0); }
        50% { transform: rotate(45deg) translateY(100%); }
      }

      .nia-brain-icon {
        position: relative;
        width: 45px;
        height: 45px;
      }

      .brain-svg {
        width: 100%;
        height: 100%;
        filter: drop-shadow(0 0 8px rgba(0, 243, 255, 0.8));
      }

      .brain-outline {
        animation: brainPulse 2s ease-in-out infinite;
      }

      @keyframes brainPulse {
        0%, 100% { stroke-width: 2; opacity: 1; }
        50% { stroke-width: 3; opacity: 0.7; }
      }

      .neuron {
        animation: neuronBlink 1.5s ease-in-out infinite;
      }

      .neuron-1 { animation-delay: 0s; }
      .neuron-2 { animation-delay: 0.3s; }
      .neuron-3 { animation-delay: 0.6s; }
      .neuron-4 { animation-delay: 0.9s; }
      .neuron-5 { animation-delay: 1.2s; }

      @keyframes neuronBlink {
        0%, 100% { opacity: 1; r: 3; }
        50% { opacity: 0.3; r: 2; }
      }

      .synapse {
        animation: synapseFlow 2s linear infinite;
      }

      @keyframes synapseFlow {
        0%, 100% { stroke-dasharray: 0 100; opacity: 0.2; }
        50% { stroke-dasharray: 100 0; opacity: 0.8; }
      }

      .nia-pulse {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
        height: 100%;
        border: 2px solid #00f3ff;
        border-radius: 50%;
        animation: niaPulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
      }

      @keyframes niaPulse {
        0% {
          transform: translate(-50%, -50%) scale(0.8);
          opacity: 1;
        }
        100% {
          transform: translate(-50%, -50%) scale(1.5);
          opacity: 0;
        }
      }

      .nia-name {
        font-size: 9px;
        color: #00f3ff;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-top: 3px;
        font-weight: 600;
        text-shadow: 0 0 10px rgba(0, 243, 255, 0.8);
      }

      #nia-panel {
        position: fixed;
        bottom: 120px;
        right: 30px;
        width: 380px;
        max-height: 600px;
        background: linear-gradient(135deg, #0a1929 0%, #1a2942 100%);
        border: 2px solid #00f3ff;
        border-radius: 20px;
        box-shadow: 
          0 0 30px rgba(0, 243, 255, 0.4),
          0 20px 60px rgba(0, 0, 0, 0.8);
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      }

      #nia-panel.nia-hidden {
        opacity: 0;
        transform: translateY(20px) scale(0.95);
        pointer-events: none;
      }

      .nia-header {
        background: linear-gradient(135deg, #001a33 0%, #003366 100%);
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #00f3ff;
      }

      .nia-header h3 {
        margin: 0;
        color: #00f3ff;
        font-size: 18px;
        display: flex;
        align-items: center;
        gap: 8px;
        text-shadow: 0 0 10px rgba(0, 243, 255, 0.6);
      }

      .nia-icon {
        font-size: 24px;
        animation: float 3s ease-in-out infinite;
      }

      @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
      }

      .nia-status {
        font-size: 11px;
        color: #00ff88;
        margin-left: 10px;
        animation: statusBlink 2s infinite;
      }

      @keyframes statusBlink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
      }

      .nia-close {
        background: transparent;
        border: none;
        color: #00f3ff;
        font-size: 24px;
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
      }

      .nia-close:hover {
        color: #ff4466;
        transform: rotate(90deg);
      }

      .nia-content {
        padding: 20px;
      }

      .nia-intro {
        background: rgba(0, 243, 255, 0.1);
        border-left: 3px solid #00f3ff;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
      }

      .nia-intro p {
        margin: 0 0 10px 0;
        color: #e0f7ff;
        font-size: 14px;
        line-height: 1.6;
      }

      .nia-intro p:last-child {
        margin-bottom: 0;
      }

      .nia-specs {
        font-size: 12px !important;
        color: #00d4ff !important;
        margin-top: 12px !important;
        line-height: 1.8 !important;
        opacity: 0.9;
      }

      .nia-messages {
        max-height: 300px;
        overflow-y: auto;
        margin-bottom: 20px;
        padding-right: 10px;
      }

      .nia-messages::-webkit-scrollbar {
        width: 6px;
      }

      .nia-messages::-webkit-scrollbar-track {
        background: rgba(0, 243, 255, 0.1);
        border-radius: 3px;
      }

      .nia-messages::-webkit-scrollbar-thumb {
        background: #00f3ff;
        border-radius: 3px;
      }

      .nia-message {
        background: rgba(0, 153, 255, 0.15);
        border-left: 3px solid #0099ff;
        padding: 12px 15px;
        margin-bottom: 12px;
        border-radius: 8px;
        color: #e0f7ff;
        font-size: 13px;
        line-height: 1.6;
        animation: messageSlide 0.4s ease-out;
      }

      @keyframes messageSlide {
        from {
          opacity: 0;
          transform: translateX(20px);
        }
        to {
          opacity: 1;
          transform: translateX(0);
        }
      }

      .nia-message strong {
        color: #00f3ff;
      }

      .nia-controls {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
      }

      .nia-btn {
        flex: 1;
        min-width: 150px;
        padding: 12px 16px;
        background: linear-gradient(135deg, #0066cc 0%, #0099ff 100%);
        border: 1px solid #00f3ff;
        border-radius: 10px;
        color: white;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(0, 153, 255, 0.3);
      }

      .nia-btn:hover {
        background: linear-gradient(135deg, #0099ff 0%, #00ccff 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 153, 255, 0.5);
      }

      .nia-btn:active {
        transform: translateY(0);
      }

      @media (max-width: 768px) {
        #nia-panel {
          right: 15px;
          left: 15px;
          width: auto;
          bottom: 110px;
        }
        
        #nia-widget {
          bottom: 20px;
          right: 20px;
        }
      }
    `;
    document.head.appendChild(style);
  }

  animerCerveau() {
    // Animations supplémentaires si nécessaire
    setInterval(() => {
      const neurones = document.querySelectorAll('.neuron');
      neurones.forEach((n, i) => {
        setTimeout(() => {
          n.style.fill = `hsl(${190 + Math.random() * 20}, 100%, ${60 + Math.random() * 20}%)`;
        }, i * 100);
      });
    }, 3000);
  }

  ajouterEvenements() {
    const widget = document.getElementById('nia-widget');
    widget.addEventListener('click', () => this.ouvrir());
  }

  ouvrir() {
    const panel = document.getElementById('nia-panel');
    panel.classList.remove('nia-hidden');
    this.isActive = true;
    
    // Message d'accueil contextuel
    if (!this.messageInitial) {
      this.ajouterMessage(this.genererMessageContextuel());
      this.messageInitial = true;
    }
  }

  fermer() {
    const panel = document.getElementById('nia-panel');
    panel.classList.add('nia-hidden');
    this.isActive = false;
  }

  ajouterMessage(texte) {
    const messagesDiv = document.getElementById('nia-messages');
    const message = document.createElement('div');
    message.className = 'nia-message';
    message.innerHTML = texte;
    messagesDiv.appendChild(message);
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
  }

  genererMessageContextuel() {
    const page = document.title.toLowerCase();
    
    if (page.includes('musée') || page.includes('museum')) {
      return `Je détecte que vous explorez le <strong>Musée Virtuel</strong>. Ces œuvres représentent mon parcours : de l'enfance dans la rue à la reconstruction par l'écriture et la tech. Chaque tableau est une étape de mon évolution psychologique grâce à ma dyslexie transformée en force mentale (HPI 136 QI).`;
    } else if (page.includes('bibliothèque') || page.includes('library') || page.includes('livre')) {
      return `Bienvenue dans la <strong>Bibliothèque</strong>. Ces récits autobiographiques tracent mon évolution : hypervigilance développée dans la survie, pensée divergente de la dyslexie, et vision inclusive du monde tech. Chaque livre est un fragment de ma psyché.`;
    } else {
      return `Je suis là pour vous guider dans l'univers <strong>AsArt'sDev</strong>. Mon intelligence est façonnée par l'expérience unique d'Asmir : dyslexie-HPI, résilience urbaine, et vision d'une tech inclusive pour tous.`;
    }
  }

  parlerContexte() {
    const contextes = {
      'musée': `🎨 <strong>Musée Virtuel :</strong> Galerie d'œuvres numériques édition limitée. Chaque tableau raconte une phase de reconstruction personnelle. La dyslexie m'a forcé à développer une vision spatiale et symbolique unique, transformée ici en art digital.`,
      'bibliothèque': `📚 <strong>Bibliothèque Dynamique :</strong> Collection de récits autobiographiques. "Max et Mila" (enfance), "Les Trois Vies" (adulte), "Vision Technologique" (futur). Ma dyslexie a créé une narration non-linéaire, reflétant ma pensée en arborescence HPI.`,
      'default': `💡 <strong>AsArt'sDev :</strong> Plateforme créative fusionnant art, écriture et tech. Née d'un parcours atypique : rue → survie → reconstruction. Ma dyslexie et mon HPI (136 QI) ont forgé une vision du monde en 3D mental, où chaque difficulté devient innovation.`
    };

    const page = document.title.toLowerCase();
    let message = contextes.default;
    
    if (page.includes('musée')) message = contextes.musée;
    if (page.includes('bibliothèque') || page.includes('livre')) message = contextes.bibliothèque;
    
    this.ajouterMessage(message);
  }

  expliquerVision() {
    const vision = `
      🌟 <strong>Ma Vision du Monde :</strong><br><br>
      
      🧩 <strong>Dyslexie = Force mentale</strong> : Pensée en 3D, solutions non-conventionnelles, créativité débridée.<br><br>
      
      🧠 <strong>HPI/HPE 136 QI</strong> : Hypervigilance transformée en analyse systémique, anticipation stratégique.<br><br>
      
      🚀 <strong>Tech Inclusive</strong> : IA éthique, accessibilité malvoyants, open-source médical. La tech doit servir l'humain, pas l'inverse.<br><br>
      
      💫 <strong>De la Rue au Labo</strong> : Parcours autodidacte prouve que l'innovation naît de la résilience et de la différence assumée.
    `;
    
    this.ajouterMessage(vision);
  }
}

// Initialisation automatique
let milianNIA;
document.addEventListener('DOMContentLoaded', () => {
  milianNIA = new MillianNIA();
  console.log('🧠 Milian-NIA initialisée avec succès');
});
