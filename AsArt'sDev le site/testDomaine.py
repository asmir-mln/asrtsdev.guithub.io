import requests

domaines = [
    "https://asartsdev.fr",
    "https://asartsdev.com",
    "https://asartsdev.tech",
    "https://asmir-mln.github.io/asrtsdev.guithub.io"
]

for d in domaines:
    try:
        r = requests.get(d, timeout=5)
        print(d, "actif - status:", r.status_code)
    except:
        print(d, "non actif ou inexistant")