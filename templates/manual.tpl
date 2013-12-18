<h3>Documentație</h3>

<p>Bursuca este un joc care simulează o bursă de acțiuni. Nu cunosc originea acestui joc, dar l-am învățat acum 100 de ani de la <a
href="http://budiu.info/mihai.html">Mihai Budiu</a>. Acest site creează un cadru în care utilizatorii pot programa strategii de joc pentru Bursuca și
pot organiza turnee între aceste strategii.</p>

<h4>Regulile jocului</h4>

<p>Bursuca este un joc de simulare a unei burse de acțiuni, pentru 2-6 jucători, care folosește două zaruri și un tabel. „Bursuca”, deci, este
diminutivul de la „bursă” (ce altceva?).</p>

<p>Există șase companii, numerotate de la 1 la 6. Inițial, acțiunile fiecărei companii au valori aleatoare, între $2 și $6. Jucătorii încep partida cu
câte $10 cash. Pe rând, fiecare jucător aruncă o pereche de zaruri. Să zicem că dă 5-3. El trebuie să execute exact una dintre aceste 8 acțiuni:</p>

<ul>
  <li>cumpără 5 acțiuni la Compania 3 sau 3 acțiuni la Compania 5, dacă are destul cash;</li>
  <li>vinde 5 acțiuni de la Compania 3 sau 3 acțiuni de la Compania 5, dacă le deține;</li>
  <li>scade prețul acțiunilor Companiei 3 cu 5 puncte sau ale Companiei 5 cu 3 puncte, dacă prin această scădere prețul rămâne >= 1;</li>
  <li>crește prețul acțiunilor Companiei 3 cu 5 puncte sau ale Companiei 5 cu 3 puncte.</li>
</ul>

<p>Creșterea prețului este întotdeauna posibilă, deci întotdeauna vor exista mutări legale. După cumpărarea de acțiuni, prețul crește cu $1 pentru
fiecare 3 acțiuni cumpărate, rotunjit în jos. De exemplu, dacă jucătorul cumpără 5 acțiuni la o companie, prețul acțiunilor crește cu $1. După o
vânzare, prețul scade similar (doar că prețul nu poate scădea sub 1).</p>

<p>Jocul se încheie când un jucător acumulează cel puțin $100 cash. Clasamentul final este dat de cash-ul jucătorilor, ordonat descrescător.</p>

<h4>Strategie</h4>

<p>Desigur, strategia de bază este: cumpără ieftin, crește prețul, vinde scump, repetă. Jocul este însă complex și mult mai interesant de atât, mai
ales cu 4-5 jucători. Jucătorii vor încerca să-și saboteze unul altuia investițiile coborând prețurile la companii la care adversarii au acțiuni
etc.</p>

<h4>Specificațiile agenților</h4>

<p>Pe acest site, programele se numesc agenți. Un agent este un program scris de voi care își măsoară puterile cu alți agenți. Site-ul se ocupă de
interconectarea agenților și de arbitrarea partidelor.</p>

<p>Momentan site-ul acceptă doar agenți scriși în C/C++. Ei vor fi compilați cu comenzile:

<ul>
  <li>C (extensia <code>.c</code>): <code>gcc -Wall -O2 -static -lm -o &lt;executabil&gt; &lt.agent.c&gt;</code></li>
  <li>C++ (extensia <code>.cpp</code>): <code>g++ -Wall -O2 -static -std=c++0x -lm -o &lt;executabil&gt; &lt.agent.cpp&gt;</code></li>
</ul>

<p>Agentul va citi și scrie la intrarea / ieșirea standard. La începutul jocului, agentul tău va primi starea inițială a jocului, respectiv:</p>

<pre>
  N T U<sub>1</sub> V<sub>1</sub> U<sub>2</sub> V<sub>2</sub> ... U<sub>N-1</sub> V<sub>N-1</sub>
  P<sub>1</sub> P<sub>2</sub> P<sub>3</sub> P<sub>4</sub> P<sub>5</sub> P<sub>6</sub>
</pre>

<p>cu semnificația:</p>

<ul>
  <li><code>N</code> este numărul de jucători.</li>

  <li>1 ≤ <code>T</code> ≤ N este numărul de ordine al agentului tău în acest joc (ca să știi când trebuie să muți și când trebuie să citești mutările
  adversarilor).</li>

  <li><code>U<sub>i</sub> V<sub>i</sub></code> sunt ID-ul de utilizator și versiunea de program a fiecărui adversar (propriii tăi parametri lipsesc
  din șir). Așadar, dacă <code>N</code> = 4 și <code>T</code> = 2, vei primi informații despre jucătorii 1, 3 și 4. Agentul tău poate ignora aceste
  informații sau le poate folosi pentru a încerca să învețe strategiile agenților adverși (vezi mai jos).</li>

  <li><code>P<sub>1</sub> ... P<sub>6</sub></code> sunt prețurile inițiale ale acțiunilor.
</ul>

<p>Când este rândul lui, agentul va trebui să citească o linie de forma:</p>

<pre>
  Z<sub>1</sub> Z<sub>2</sub>
</pre>

<p>, respectiv valorile zarurilor pe care le-a dat, și să răspundă sub forma:</p>

<ul>
  <li><code>B x y</code> &mdash; cumpără x acțiuni la compania y (buy)</li>
  <li><code>S x y</code> &mdash; vinde x acțiuni de la compania y (sell)</li>
  <li><code>L x y</code> &mdash; scade prețul cu $x la acțiunile companiei y (lower)</li>
  <li><code>R x y</code> &mdash; ridică prețul cu $x la acțiunile companiei y (raise)</li>
</ul>

<p>Evident, <code>x</code> și <code>y</code> trebuie să fie <code>Z<sub>1</sub></code> și <code>Z<sub>2</sub></code>, posibil inversate.</p>

<p>După fiecare linie afișată, este <b>esențial</b> să golești bufferul de ieșire cu comenzile <code>fflush(stdout);</code> sau
<code>cout.flush();</code>. În caz contrar, este posibil ca datele tipărite să rămână în buffer, iar arbitrul să nu știe că ai tipărit ceva.</p>

<p>Când <b>nu</b> este rândul lui, agentul va trebui să citească acțiunea jucătorului la mutare, sub forma:</p>

<ul>
  <li><code>B x y</code></li>
  <li><code>S x y</code></li>
  <li><code>L x y</code></li>
  <li><code>R x y</code></li>
  <li><code>P 0 0</code></li>
</ul>

<p>Comenzile B, S, L și R au semnificația de mai sus. Comanda <code>P 0 0</code> (pas) arată că adversarul a fost eliminat din joc. Dacă un adversar
spune „pas”, el va continua să spună „pas” până la sfârșitul jocului.</p>

<h4>Restricții</h4>

<ul>
  <li>2 ≤ <code>N</code> ≤ 10</li>
  <li><code>N</code> va fi constant pentru toate partidele dintr-un turneu, cel mai probabil <code>N</code> = 4</li>    
  <li>2 ≤ <code>P<sub>1</sub> ... P<sub>6</sub></code> ≤ 6, generate aleator</li>
  <li>Timpul maxim de gândire este 1 sec. / mutare.</li>
  <li>Memoria permisă este 16 MB.</li>
  <li>Mărimea fișierului sursă nu poate depăși 64 KB.</li>
</ul>

<h4>Eliminarea din joc</h4>

<p>Agenții sunt eliminați din joc:</p>

<ul>
  <li>când fac o mutare incorectă (mută alte zaruri sau vor să cumpere, dar nu au destui bani etc.);</li>
  <li>când depășesc timpul de gândire;</li>
  <li>când alocă prea multă memorie;</li>
  <li>când programul se termină înainte de sfârșitul jocului.</li>
</ul>

<p>În clasamentul final, primii vor fi agenții care au terminat jocul cu bine, în ordinea descrescătoare a cash-ului. Urmează agenții care au fost
eliminați din joc, în ordine descrescătoare a mutării la care au fost eliminați.</p>

<h4>Salvarea datelor între jocuri</h4>

<p>Agentul tău poate citi și scrie din fișierul <code>bursuca.dat</code> pentru a-și păstra date pe server de la o partidă la alta. Poți folosi acest
fișier, de exemplu, pentru a învăța strategiile agenților adverși.</p>

<p>Limita de mărime este de <b>1 MB</b> (1048576 octeți). Dacă creezi un fișier <code>bursuca.dat</code> mai mare de 1 MB, sistemul nu îl va
păstra. De asemenea, orice alt fișier creat nu va fi salvat între partide.</p>

<h4>Testarea programelor</h4>

<p>Serverul nu face verificări de corectitudine a agenților tăi, dincolo de semnalarea erorilor de compilare. Îți recomandăm să îți testați programele
înainte de a le încărca pe server, de exemplu folosind un fișier de intrare. Creează un fișier <code>comenzi.in</code> cu conținutul:</p>

<pre>
  3 2 101 102 103 104
  2 3 4 5 6 6
  L 3 1
  4 3
  L 2 5
</pre>

<p>Apoi rulează programul tău cu aceste date de intrare:</p>

<pre>
  programul_meu < comenzi.in
</pre>

<h4>Exemplu de cod-sursă</h4>

<p>Iată <a href="download/dummy.cpp">un exemplu de cod-sursă</a>. Acest agent joacă corect, dar are o strategie absolut naivă.</p>

<h4>Utilizarea acestui server</h4>

<p>Serverul este minimalist. Sperăm să nu îți pună probleme.</p>

<ul>
  <li>Conectează-te folosind orice OpenID. Dacă ai un cont de Google sau de Yahoo, el servește deja ca OpenID. Nu este obligatoriu să îți dai numele
  real.</li>

  <li>Încarcă unul sau mai mulți agenți.</li>

  <li>Creează partide cu acești agenți. Poți provoca unul sau mai mulți agenți adverși.</li>

  <li>După crearea partidei, evaluatorul arbitrează acea partidă, de obicei în câteva secunde. Apoi poți urmări reluarea, mutare cu mutare.</li>

  <li>Asigură-te că agenții tăi funcționează corect punându-i să joace cu diferite versiuni ale utilizatorului <b>cata.</b></li>

</ul>

<h4>Coeficientul ELO</h4>

<p>Fiecare utilizator are un coeficient ELO, similar celui folosit la jocul de șah. Acesta pornește de la 1.600, crește de câte ori utilizatorul
câștigă o partidă și scade de câte ori utilizatorul pierde o partidă (toate locurile în afară de locul I sunt pierzătoare). Formula exactă de calcul
este <a href="http://en.wikipedia.org/wiki/Elo_rating_system#Mathematical_details">cea de la Wikipedia</a> cu constantele D = 400 (constanta de
disparitate) și K = 32.</p>

<p>Ce înseamnă, tradusă în cuvinte, această formulă? Când doi jucători de coeficient ELO egal joacă o partidă, câștigătorul va primi 16 puncte, iar
pierzătorul va pierde 16 puncte. Cu cât jucătorul câștigător este mai bine cotat, cu atât transferul de puncte este mai mic, căci surpriza este mai
mică dacă el câștigă. De exemplu, dacă diferența între cei doi este de 400 de puncte, jucătorul favorit va câștiga doar 3 puncte în cazul în care
câștigă, iar învinsul va pierde doar 3 puncte. Similar, dacă un jucător câștigă o partidă în fața altuia cu 400 de puncte mai bine cotat, transferul
de puncte va fi de 29 de puncte.</p>

<p>În fiecare meci, câștigătorul obține puncte conform acestei formule de la fiecare dintre pierzători. Există două excepții:</p>

<ul>
  <li>Când două copii ale aceluiași agent participă la joc, iar una dintre ele câștigă, între cele două copii nu se face un transfer de puncte.</li>

  <li>Când unul dintre agenți este marcat ca „unrated” (disponibil doar pentru partide neoficiale), el nici nu va câștiga, nici nu va ceda puncte. În
  mod normal, doar ultima versiune de agent a fiecărui utilizator câștigă puncte. Când cineva încarcă un agent nou, cei anteriori devin
  neoficiali.</li>

</ul>

<h4>Turnee</h4>

<p>Un turneu cu <code>N</code> participanți, cu <code>P</code> agenți în fiecare partidă și cu <code>R</code> runde se desfășoară astfel:</p>

<ul>

  <li>N trebuie să fie multiplu de <code>P</code>.</li>
  <li>Participanții sunt grupați câte <code>P</code>, în mod aleator, și se desfășoară prima rundă.</li>
  <li>Se procedează similar pentru toate cele <code>R</code> runde.</li>
  <li>Câștigătorul fiecărei partide primește 1 punct.</li>
  <li>Participantul cu cele mai multe puncte este câștigătorul turneului.</li>

</ul>

<p>La turneul final al cercului de informatică vor participa numai ultimele versiuni ale fiecărui utilizator.</p>
