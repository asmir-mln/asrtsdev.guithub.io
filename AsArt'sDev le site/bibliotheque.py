from flask import Flask, jsonify

app = Flask(__name__)

livres = {
    "enfant": [
        {
            "id": 1,
            "titre": "Max, Mila et le Perroquet"
        }
    ],
    "ados": [
        {
            "id": 2,
            "titre": "Max, Mila – Cycle des 10 ans"
        }
    ],
    "adultes": [
        {
            "id": 3,
            "titre": "Le Dernier Maboï"
        }
    ]
}

@app.route("/api/livres/<categorie>")
def get_livres(categorie):
    return jsonify(livres.get(categorie, []))

if __name__ == "__main__":
    app.run(port=5000)