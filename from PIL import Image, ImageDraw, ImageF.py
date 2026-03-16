from PIL import Image, ImageDraw, ImageFont
import imageio
from tqdm import tqdm

years = list(range(2026, 2037))
images = []

for i, year in enumerate(tqdm(years)):
    # Créer une image
    img = Image.new('RGB', (800, 600), color=(30 + i*20, 30 + i*15, 30 + i*10))
    draw = ImageDraw.Draw(img)

    # Texte de l'année
    font = ImageFont.load_default()
    draw.text((300, 50), f"Année: {year}", fill=(255,255,255), font=font)

    # Simuler la ville : rectangles représentant bâtiments qui grandissent
    for j in range(10):
        x0 = 50 + j*70
        y0 = 500 - (i+1)*15 - j*3
        x1 = x0 + 50
        y1 = 500
        draw.rectangle([x0, y0, x1, y1], fill=(100+i*10, 150+i*8, 200-i*5))

    # Sauver l'image
    filename = f"city_{year}.png"
    img.save(filename)
    images.append(filename)

# Créer un GIF
frames = [imageio.imread(img) for img in images]
imageio.mimsave('evolution_ville.gif', frames, duration=0.8)

print("GIF créé : evolution_ville.gif")