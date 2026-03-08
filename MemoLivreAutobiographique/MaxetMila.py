from reportlab.lib.pagesizes import A4
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer
from reportlab.lib.styles import getSampleStyleSheet

# Nom du fichier PDF
filename = "mon_livre.pdf"

# Création du document
doc = SimpleDocTemplate(filename, pagesize=A4,
                        rightMargin=50, leftMargin=50,
                        topMargin=50, bottomMargin=50)

# Styles
styles = getSampleStyleSheet()
style_normal = styles['Normal']
style_title = styles['Title']

# Contenu
contenu = [
    Paragraph("Mon Livre Génial", style_title),
    Spacer(1, 20),
    Paragraph("Chapitre 1 : Le commencement", style_title),
    Paragraph("Voici le texte du premier chapitre.", style_normal),
    Spacer(1, 10),
    Paragraph("Chapitre 2 : La suite de l'histoire", style_title),
    Paragraph("Le texte continue ici.", style_normal),
]

# Générer le PDF
doc.build(contenu)

print(f"PDF '{filename}' créé avec succès !")