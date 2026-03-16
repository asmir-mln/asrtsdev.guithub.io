import whois

domaines = [
    "asartsdev.fr",
    "asartsdev.com",
    "asartsdev.tech"
]

for domaine in domaines:
    try:
        info = whois.whois(domaine)
        if info.domain_name:
            print(f"{domaine} est déjà pris")
        else:
            print(f"{domaine} est disponible")
    except:
        print(f"{domaine} semble disponible") api este oai functuib ou