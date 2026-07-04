/**
 * HitJam Modulair Component: JBL PartyBox LED-Ring Effect
 */

/**
 * Zet de JBL Rave-modus aan of uit
 * @param {boolean} status - true voor draaien/flitsen, false voor rustige modus
 */
function toggleJBLPartyBox(status = true) {
    const app = document.getElementById('hitjamApp');
    if (!app) return;

    // Controleer of de fysieke ring al bestaat, zo niet: maak hem aan!
    let jblRing = document.getElementById('jblLedRing');
    if (!jblRing) {
        jblRing = document.createElement('div');
        jblRing.id = 'jblLedRing';
        jblRing.classList.add('jbl-partybox-bg');
        // Schuif hem helemaal vooraan in de container (achter de knoppen)
        app.insertBefore(jblRing, app.firstChild);
    }

    if (status) {
        app.classList.add('jbl-rave-active');
    } else {
        app.classList.remove('jbl-rave-active');
    }
}
