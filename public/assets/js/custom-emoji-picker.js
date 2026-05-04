// Custom Emoji Picker with German & English Search Support
// 500+ Emojis organized by categories with Lazy Loading

const EMOJI_DATA = {
    // Smileys & Emotion (70+ emojis)
    smileys: [
        { emoji: '😀', keywords: ['lächeln', 'glücklich', 'freude', 'smile', 'happy'] },
        { emoji: '😃', keywords: ['lächeln', 'glücklich', 'freude', 'smile'] },
        { emoji: '😄', keywords: ['lächeln', 'freude', 'lachen', 'smile', 'laugh'] },
        { emoji: '😁', keywords: ['grinsen', 'zähne', 'lächeln', 'grin'] },
        { emoji: '😆', keywords: ['lachen', 'freude', 'lächeln', 'laugh'] },
        { emoji: '😅', keywords: ['schwitzen', 'lächeln', 'erleichtert', 'sweat', 'relief'] },
        { emoji: '😂', keywords: ['lachen', 'tränen', 'lustig', 'laugh', 'tears', 'funny'] },
        { emoji: '🤣', keywords: ['lachen', 'rollen', 'lustig', 'rofl'] },
        { emoji: '😊', keywords: ['lächeln', 'glücklich', 'freundlich', 'smile', 'happy'] },
        { emoji: '😇', keywords: ['engel', 'heiligenschein', 'unschuldig', 'angel', 'innocent'] },
        { emoji: '🙂', keywords: ['lächeln', 'leicht', 'smile'] },
        { emoji: '🙃', keywords: ['kopfüber', 'verkehrt', 'sarkasmus', 'upside down'] },
        { emoji: '😉', keywords: ['zwinkern', 'flirten', 'wink', 'flirt'] },
        { emoji: '😌', keywords: ['erleichtert', 'zufrieden', 'relieved', 'satisfied'] },
        { emoji: '😍', keywords: ['liebe', 'herz', 'verliebt', 'love', 'hearts'] },
        { emoji: '🥰', keywords: ['liebe', 'herzen', 'verliebt', 'love', 'hearts'] },
        { emoji: '😘', keywords: ['kuss', 'herz', 'liebe', 'kiss', 'love'] },
        { emoji: '😗', keywords: ['kuss', 'pfeifen', 'kiss', 'whistle'] },
        { emoji: '😙', keywords: ['kuss', 'lächeln', 'kiss', 'smile'] },
        { emoji: '😚', keywords: ['kuss', 'augen zu', 'kiss', 'closed eyes'] },
        { emoji: '😋', keywords: ['lecker', 'essen', 'zunge', 'yummy', 'food'] },
        { emoji: '😛', keywords: ['zunge', 'spielerisch', 'tongue', 'playful'] },
        { emoji: '😜', keywords: ['zwinkern', 'zunge', 'wink', 'tongue'] },
        { emoji: '🤪', keywords: ['verrückt', 'wild', 'crazy', 'wild'] },
        { emoji: '😝', keywords: ['zunge', 'geschlossen', 'tongue', 'closed'] },
        { emoji: '🤑', keywords: ['geld', 'reich', 'dollar', 'money', 'rich'] },
        { emoji: '🤗', keywords: ['umarmung', 'hug'] },
        { emoji: '🤭', keywords: ['hand', 'mund', 'kichern', 'hand over mouth', 'giggle'] },
        { emoji: '🤫', keywords: ['leise', 'psst', 'geheim', 'quiet', 'shh', 'secret'] },
        { emoji: '🤔', keywords: ['denken', 'nachdenken', 'überlegen', 'think', 'wondering'] },
        { emoji: '🤐', keywords: ['reißverschluss', 'mund', 'zipper mouth'] },
        { emoji: '🤨', keywords: ['augenbraue', 'skeptisch', 'raised eyebrow'] },
        { emoji: '😐', keywords: ['neutral', 'ausdruckslos', 'neutral face'] },
        { emoji: '😑', keywords: ['ausdruckslos', 'expressionless'] },
        { emoji: '😶', keywords: ['ohne mund', 'stille', 'no mouth'] },
        { emoji: '😏', keywords: ['grinsend', 'smirking'] },
        { emoji: '😒', keywords: ['unbeeindruckt', 'unamused'] },
        { emoji: '🙄', keywords: ['augen rollen', 'eye roll'] },
        { emoji: '😬', keywords: ['grimasse', 'grimacing'] },
        { emoji: '🤥', keywords: ['lügen', 'pinocchio', 'lying'] },
        { emoji: '😪', keywords: ['schläfrig', 'sleepy'] },
        { emoji: '😴', keywords: ['schlafend', 'sleeping'] },
        { emoji: '😷', keywords: ['maske', 'krank', 'mask', 'sick'] },
        { emoji: '🤒', keywords: ['fieber', 'krank', 'fever', 'sick'] },
        { emoji: '🤕', keywords: ['verletzt', 'injured'] },
        { emoji: '🤢', keywords: ['übel', 'nauseated'] },
        { emoji: '🤮', keywords: ['erbrechen', 'vomiting'] },
        { emoji: '🤧', keywords: ['niesen', 'sneezing'] },
        { emoji: '🥵', keywords: ['heiß', 'hot'] },
        { emoji: '🥶', keywords: ['kalt', 'cold'] },
        { emoji: '🥳', keywords: ['party', 'feier', 'geburtstag', 'party', 'celebrate', 'birthday'] },
        { emoji: '🥸', keywords: ['verkleidung', 'disguise'] },
        { emoji: '😎', keywords: ['sonnenbrille', 'cool', 'sunglasses', 'cool'] },
        { emoji: '🤓', keywords: ['nerd', 'brille', 'nerd', 'glasses'] },
        { emoji: '🧐', keywords: ['monokel', 'untersuchen', 'monocle', 'investigate'] },
        { emoji: '😈', keywords: ['teufel', 'böse', 'devil', 'evil'] },
        { emoji: '👿', keywords: ['wütend', 'teufel', 'angry', 'devil'] },
        { emoji: '💀', keywords: ['totenkopf', 'tot', 'skull', 'dead'] },
        { emoji: '☠️', keywords: ['totenkopf', 'gefahr', 'skull', 'danger'] },
        { emoji: '💩', keywords: ['kacke', 'poop', 'dung'] },
        { emoji: '🤡', keywords: ['clown', 'clown'] },
        { emoji: '👻', keywords: ['geist', 'gespenst', 'ghost', 'spooky'] },
        { emoji: '👽', keywords: ['alien', 'außerirdisch', 'alien'] },
        { emoji: '👾', keywords: ['spiel', 'monster', 'game', 'alien'] },
        { emoji: '🤖', keywords: ['roboter', 'robot', 'bot'] },
        { emoji: '😺', keywords: ['katze', 'lächeln', 'cat', 'smile'] },
        { emoji: '😸', keywords: ['katze', 'freude', 'cat', 'joy'] },
        { emoji: '😹', keywords: ['katze', 'lachen', 'cat', 'laugh'] },
        { emoji: '😻', keywords: ['katze', 'liebe', 'cat', 'love'] },
        { emoji: '😼', keywords: ['katze', 'sarkasmus', 'cat', 'smirk'] },
        { emoji: '😽', keywords: ['katze', 'kuss', 'cat', 'kiss'] },
        { emoji: '🙀', keywords: ['katze', 'schock', 'cat', 'shocked'] },
        { emoji: '😿', keywords: ['katze', 'weinen', 'cat', 'cry'] },
        { emoji: '😾', keywords: ['katze', 'böse', 'cat', 'angry'] },
        // More face emojis (commonly missing)
        { emoji: '🥲', keywords: ['schmelzen', 'traurig lächeln', 'melting', 'sad smile', 'relief'] },
        { emoji: '🥹', keywords: ['tränen', 'bitten', 'weinen', 'pleading', 'holding back tears'] },
        { emoji: '🥺', keywords: ['bitten', 'bitte', 'traurig', 'pleading', 'puppy eyes'] },
        { emoji: '🤠', keywords: ['cowboy', 'hut', 'cowboy', 'hat'] },
        { emoji: '😳', keywords: ['erröten', 'verlegen', 'flushed', 'embarrassed'] },
        { emoji: '🥱', keywords: ['gähnen', 'müde', 'yawn', 'tired', 'bored'] },
        { emoji: '🤤', keywords: ['sabbern', 'hungrig', 'drooling', 'hungry'] },
        { emoji: '😵', keywords: ['schwindlig', 'benommen', 'dizzy'] },
        { emoji: '😵‍💫', keywords: ['schwindlig', 'spirale', 'dizzy', 'spiral'] },
        { emoji: '🤯', keywords: ['kopf explodiert', 'schock', 'exploding head', 'mind blown'] },
        { emoji: '😲', keywords: ['erstaunt', 'überrascht', 'astonished', 'surprised'] },
        { emoji: '😮', keywords: ['mund offen', 'überrascht', 'open mouth', 'surprised'] },
        { emoji: '😯', keywords: ['still', 'überrascht', 'hushed', 'surprised'] },
        { emoji: '😧', keywords: ['qual', 'ängstlich', 'anguished', 'anxious'] },
        { emoji: '😦', keywords: ['enttäuscht', 'mund offen', 'frowning', 'open mouth'] },
        { emoji: '🥴', keywords: ['schwindlig', 'krank', 'woozy', 'drunk', 'dizzy'] },
        { emoji: '😤', keywords: ['dampf', 'wütend', 'steam', 'angry', 'frustrated'] },
        { emoji: '😠', keywords: ['wütend', 'böse', 'angry', 'mad'] },
        { emoji: '😡', keywords: ['wütend', 'sehr böse', 'angry', 'rage'] },
        { emoji: '🤬', keywords: ['fluchen', 'schimpfen', 'cursing', 'swearing'] },
        { emoji: '😱', keywords: ['schreien', 'angst', 'scream', 'fear'] },
        { emoji: '😨', keywords: ['ängstlich', 'fearful'] },
        { emoji: '😰', keywords: ['nervös', 'anxious', 'sweat'] },
        { emoji: '😥', keywords: ['traurig', 'erleichtert', 'sad', 'relieved'] },
        { emoji: '😓', keywords: ['schweiß', 'mühe', 'sweat', 'effort'] },
        { emoji: '🫠', keywords: ['schmelzen', 'peinlich', 'melting', 'embarrassed'] },
        { emoji: '🫥', keywords: ['unsichtbar', 'leer', 'dotted line', 'invisible'] },
        { emoji: '🫤', keywords: ['zweifel', 'unsicher', 'diagonal mouth', 'doubt'] },
        { emoji: '🫨', keywords: ['zittern', 'schock', 'shaking', 'shocked'] }
    ],
    
    // Gestures & Body Parts (35 emojis)
    gestures: [
        { emoji: '👍', keywords: ['daumen hoch', 'gut', 'ok', 'thumbs up', 'good'] },
        { emoji: '👎', keywords: ['daumen runter', 'schlecht', 'thumbs down', 'bad'] },
        { emoji: '👊', keywords: ['faust', 'schlag', 'fist', 'punch'] },
        { emoji: '✊', keywords: ['faust', 'stark', 'fist', 'strong'] },
        { emoji: '🤛', keywords: ['faust', 'links', 'fist bump'] },
        { emoji: '🤜', keywords: ['faust', 'rechts', 'fist bump'] },
        { emoji: '👏', keywords: ['klatschen', 'applaus', 'clap', 'applause'] },
        { emoji: '🙌', keywords: ['hände hoch', 'feiern', 'hands up', 'celebrate'] },
        { emoji: '👐', keywords: ['offene hände', 'open hands'] },
        { emoji: '🤲', keywords: ['hände', 'beten', 'palms up'] },
        { emoji: '🤝', keywords: ['händeschütteln', 'deal', 'handshake'] },
        { emoji: '🙏', keywords: ['beten', 'danke', 'pray', 'thank you'] },
        { emoji: '✍️', keywords: ['schreiben', 'stift', 'write', 'pen'] },
        { emoji: '💅', keywords: ['nagellack', 'maniküre', 'nail polish'] },
        { emoji: '🤳', keywords: ['selfie', 'foto', 'photo'] },
        { emoji: '💪', keywords: ['stark', 'muskel', 'kraft', 'strong', 'muscle', 'power'] },
        { emoji: '🦾', keywords: ['roboterarm', 'stark', 'cyborg arm'] },
        { emoji: '🦵', keywords: ['bein', 'leg'] },
        { emoji: '🦶', keywords: ['fuß', 'foot'] },
        { emoji: '👂', keywords: ['ohr', 'hören', 'ear', 'hear'] },
        { emoji: '👃', keywords: ['nase', 'riechen', 'nose', 'smell'] },
        { emoji: '👀', keywords: ['augen', 'schauen', 'eyes', 'look'] },
        { emoji: '👁️', keywords: ['auge', 'sehen', 'eye'] },
        { emoji: '🧠', keywords: ['gehirn', 'denken', 'brain', 'think'] },
        { emoji: '👅', keywords: ['zunge', 'tongue'] },
        { emoji: '👄', keywords: ['lippen', 'mund', 'lips', 'mouth'] },
        { emoji: '🫶', keywords: ['herz', 'hände', 'heart hands'] },
        { emoji: '✌️', keywords: ['frieden', 'sieg', 'peace', 'victory'] },
        { emoji: '🤞', keywords: ['finger kreuzen', 'glück', 'fingers crossed', 'luck'] },
        { emoji: '🤟', keywords: ['ich liebe dich', 'love you'] },
        { emoji: '🤘', keywords: ['rock', 'metal'] },
        { emoji: '🤙', keywords: ['anrufen', 'call me'] },
        { emoji: '👈', keywords: ['links zeigen', 'point left'] },
        { emoji: '👉', keywords: ['rechts zeigen', 'point right'] },
        { emoji: '👆', keywords: ['oben zeigen', 'point up'] },
        { emoji: '👇', keywords: ['unten zeigen', 'point down'] },
        { emoji: '☝️', keywords: ['zeigen', 'finger', 'point', 'finger'] },
        { emoji: '👋', keywords: ['winken', 'hallo', 'wave', 'hello'] },
        { emoji: '🤚', keywords: ['hand', 'fläche', 'hand', 'palm'] },
        { emoji: '🖐️', keywords: ['hand', 'offen', 'hand', 'open'] },
        { emoji: '✋', keywords: ['hand', 'stopp', 'hand', 'stop'] },
        { emoji: '🖖', keywords: ['vulkan', 'gruß', 'vulcan', 'greeting'] },
        { emoji: '👌', keywords: ['ok', 'gut', 'ok', 'good'] },
        { emoji: '🤌', keywords: ['finger', 'italien', 'pinch', 'italy'] },
        { emoji: '🤏', keywords: ['klein', 'pinzette', 'small', 'pinch'] }
    ],
    
    // Hearts & Love (25 emojis)
    hearts: [
        { emoji: '❤️', keywords: ['liebe', 'herz', 'rot', 'love', 'heart', 'red'] },
        { emoji: '🧡', keywords: ['orange', 'herz', 'orange heart'] },
        { emoji: '💛', keywords: ['gelb', 'herz', 'yellow heart'] },
        { emoji: '💚', keywords: ['grün', 'herz', 'green heart'] },
        { emoji: '💙', keywords: ['blau', 'herz', 'blue heart'] },
        { emoji: '💜', keywords: ['lila', 'herz', 'purple heart'] },
        { emoji: '🖤', keywords: ['schwarz', 'herz', 'black heart'] },
        { emoji: '🤍', keywords: ['weiß', 'herz', 'white heart'] },
        { emoji: '🤎', keywords: ['braun', 'herz', 'brown heart'] },
        { emoji: '💔', keywords: ['gebrochenes herz', 'traurig', 'broken heart', 'sad'] },
        { emoji: '❣️', keywords: ['herz', 'ausrufezeichen', 'heart exclamation'] },
        { emoji: '💕', keywords: ['zwei herzen', 'liebe', 'two hearts', 'love'] },
        { emoji: '💞', keywords: ['herzen', 'drehen', 'revolving hearts'] },
        { emoji: '💓', keywords: ['herz', 'schlagen', 'beating heart'] },
        { emoji: '💗', keywords: ['herz', 'wachsen', 'growing heart'] },
        { emoji: '💖', keywords: ['herz', 'funkeln', 'sparkling heart'] },
        { emoji: '💘', keywords: ['herz', 'pfeil', 'heart arrow'] },
        { emoji: '💝', keywords: ['herz', 'geschenk', 'heart gift'] },
        { emoji: '💟', keywords: ['herz', 'dekoration', 'heart decoration'] },
        { emoji: '❤️‍🔥', keywords: ['herz', 'feuer', 'heart fire'] },
        { emoji: '❤️‍🩹', keywords: ['herz', 'heilung', 'heart healing'] },
        { emoji: '💌', keywords: ['liebesbrief', 'love letter'] },
        { emoji: '💋', keywords: ['kuss', 'lippenstift', 'kiss', 'lipstick'] },
        { emoji: '💑', keywords: ['paar', 'liebe', 'couple', 'love'] },
        { emoji: '💏', keywords: ['kuss', 'paar', 'kiss', 'couple'] },
        { emoji: '👫', keywords: ['paar', 'hand in hand', 'couple'] },
        { emoji: '👬', keywords: ['männer', 'freundschaft', 'men', 'friendship'] },
        { emoji: '👭', keywords: ['frauen', 'freundschaft', 'women', 'friendship'] },
        { emoji: '💐', keywords: ['blumen', 'strauß', 'flowers', 'bouquet'] },
        { emoji: '🌺', keywords: ['blume', 'hibiskus', 'flower', 'hibiscus'] }
    ],
    
    // Sad & Negative Emotions (20 emojis)
    sad: [
        { emoji: '😢', keywords: ['weinen', 'tränen', 'traurig', 'cry', 'tears', 'sad'] },
        { emoji: '😭', keywords: ['weinen', 'laut', 'tränen', 'cry loudly', 'tears'] },
        { emoji: '😔', keywords: ['traurig', 'nachdenklich', 'sad', 'pensive'] },
        { emoji: '😞', keywords: ['enttäuscht', 'traurig', 'disappointed', 'sad'] },
        { emoji: '😟', keywords: ['besorgt', 'worried'] },
        { emoji: '😕', keywords: ['verwirrt', 'confused'] },
        { emoji: '🙁', keywords: ['traurig', 'unglücklich', 'sad', 'unhappy'] },
        { emoji: '😖', keywords: ['verwirrt', 'frustriert', 'confounded', 'frustrated'] },
        { emoji: '😣', keywords: ['durchhalten', 'anstrengend', 'persevere'] },
        { emoji: '😩', keywords: ['müde', 'erschöpft', 'weary', 'exhausted'] },
        { emoji: '😫', keywords: ['müde', 'frustriert', 'tired', 'frustrated'] },
        { emoji: '😤', keywords: ['dampf', 'wütend', 'steam', 'angry'] },
        { emoji: '😠', keywords: ['wütend', 'böse', 'angry', 'mad'] },
        { emoji: '😡', keywords: ['wütend', 'sehr böse', 'angry', 'rage'] },
        { emoji: '🤬', keywords: ['fluchen', 'schimpfen', 'symbole', 'cursing', 'swearing'] },
        { emoji: '😱', keywords: ['schreien', 'angst', 'scream', 'fear'] },
        { emoji: '😨', keywords: ['ängstlich', 'fearful'] },
        { emoji: '😰', keywords: ['nervös', 'anxious'] },
        { emoji: '😥', keywords: ['traurig', 'erleichtert', 'sad relieved'] },
        { emoji: '😓', keywords: ['schweiß', 'mühe', 'sweat', 'effort'] }
    ],
    
    // Animals & Nature (40 emojis)
    animals: [
        { emoji: '🐶', keywords: ['hund', 'welpe', 'tier', 'dog', 'puppy', 'animal'] },
        { emoji: '🐱', keywords: ['katze', 'kätzchen', 'tier', 'cat', 'kitten', 'animal'] },
        { emoji: '🐭', keywords: ['maus', 'tier', 'mouse', 'animal'] },
        { emoji: '🐹', keywords: ['hamster', 'tier', 'animal'] },
        { emoji: '🐰', keywords: ['hase', 'kaninchen', 'tier', 'rabbit', 'bunny', 'animal'] },
        { emoji: '🦊', keywords: ['fuchs', 'tier', 'fox', 'animal'] },
        { emoji: '🐻', keywords: ['bär', 'tier', 'bear', 'animal'] },
        { emoji: '🐼', keywords: ['panda', 'bär', 'tier', 'animal'] },
        { emoji: '🐨', keywords: ['koala', 'tier', 'animal'] },
        { emoji: '🐯', keywords: ['tiger', 'tier', 'animal'] },
        { emoji: '🦁', keywords: ['löwe', 'tier', 'lion', 'animal'] },
        { emoji: '🐮', keywords: ['kuh', 'tier', 'cow', 'animal'] },
        { emoji: '🐷', keywords: ['schwein', 'tier', 'pig', 'animal'] },
        { emoji: '🐸', keywords: ['frosch', 'tier', 'frog', 'animal'] },
        { emoji: '🐵', keywords: ['affe', 'gesicht', 'tier', 'monkey', 'face', 'animal'] },
        { emoji: '🐒', keywords: ['affe', 'affe', 'tier', 'monkey', 'animal'] },
        { emoji: '🙈', keywords: ['affe', 'nichts sehen', 'augen zu', 'monkey', 'see no evil', 'eyes'] },
        { emoji: '🙉', keywords: ['affe', 'nichts hören', 'ohren zu', 'monkey', 'hear no evil', 'ears'] },
        { emoji: '🙊', keywords: ['affe', 'nichts sagen', 'mund zu', 'monkey', 'speak no evil', 'mouth'] },
        { emoji: '🦍', keywords: ['gorilla', 'affe', 'tier', 'gorilla', 'monkey', 'animal'] },
        { emoji: '🦧', keywords: ['orangutan', 'affe', 'tier', 'orangutan', 'monkey', 'animal'] },
        { emoji: '🦄', keywords: ['einhorn', 'tier', 'unicorn', 'animal'] },
        { emoji: '🐝', keywords: ['biene', 'insekt', 'bee', 'insect'] },
        { emoji: '🦋', keywords: ['schmetterling', 'insekt', 'butterfly', 'insect'] },
        { emoji: '🌸', keywords: ['blume', 'rosa', 'flower', 'pink'] },
        { emoji: '🌹', keywords: ['rose', 'blume', 'liebe', 'flower', 'love'] },
        { emoji: '🐔', keywords: ['huhn', 'chicken'] },
        { emoji: '🐧', keywords: ['pinguin', 'penguin'] },
        { emoji: '🐦', keywords: ['vogel', 'bird'] },
        { emoji: '🦅', keywords: ['adler', 'eagle'] },
        { emoji: '🦆', keywords: ['ente', 'duck'] },
        { emoji: '🦉', keywords: ['eule', 'owl'] },
        { emoji: '🐺', keywords: ['wolf', 'wolf'] },
        { emoji: '🐗', keywords: ['wildschwein', 'boar'] },
        { emoji: '🐴', keywords: ['pferd', 'horse'] },
        { emoji: '🦓', keywords: ['zebra', 'zebra'] },
        { emoji: '🦒', keywords: ['giraffe', 'giraffe'] },
        { emoji: '🐘', keywords: ['elefant', 'elephant'] },
        { emoji: '🦏', keywords: ['nashorn', 'rhino'] },
        { emoji: '🦛', keywords: ['nilpferd', 'hippo'] },
        { emoji: '🐊', keywords: ['krokodil', 'crocodile'] },
        { emoji: '🐢', keywords: ['schildkröte', 'turtle'] },
        { emoji: '🦎', keywords: ['eidechse', 'lizard'] },
        { emoji: '🐍', keywords: ['schlange', 'snake'] },
        { emoji: '🐉', keywords: ['drache', 'dragon'] },
        { emoji: '🦖', keywords: ['dinosaurier', 'dinosaur'] },
        { emoji: '🐋', keywords: ['wal', 'whale'] },
        { emoji: '🐬', keywords: ['delfin', 'dolphin'] },
        { emoji: '🐟', keywords: ['fisch', 'fish'] },
        { emoji: '🐠', keywords: ['tropenfisch', 'tropical fish'] },
        { emoji: '🐡', keywords: ['kugelfisch', 'blowfish'] },
        { emoji: '🦈', keywords: ['hai', 'shark'] },
        { emoji: '🐙', keywords: ['krake', 'octopus'] },
        { emoji: '🦀', keywords: ['krabbe', 'crab'] },
        { emoji: '🦞', keywords: ['hummer', 'lobster'] },
        { emoji: '🦐', keywords: ['garnele', 'shrimp'] },
        { emoji: '🐚', keywords: ['muschel', 'shell'] },
        { emoji: '🦑', keywords: ['tintenfisch', 'squid'] },
        { emoji: '🌻', keywords: ['sonnenblume', 'sunflower'] },
        { emoji: '🌷', keywords: ['tulpe', 'tulip'] },
        { emoji: '🌼', keywords: ['blume', 'blüte', 'flower', 'blossom'] },
        { emoji: '🪷', keywords: ['lotus', 'lotus'] },
        { emoji: '🌴', keywords: ['palme', 'palm', 'baum', 'tree'] },
        { emoji: '🌵', keywords: ['kaktus', 'cactus'] },
        { emoji: '🍀', keywords: ['kleeblatt', 'glück', 'clover', 'luck'] },
        { emoji: '🌍', keywords: ['erde', 'welt', 'earth', 'world'] },
        { emoji: '🌎', keywords: ['erde', 'amerika', 'earth', 'americas'] },
        { emoji: '🌏', keywords: ['erde', 'asien', 'earth', 'asia'] },
        { emoji: '🌙', keywords: ['mond', 'nacht', 'moon', 'night'] },
        { emoji: '⭐', keywords: ['stern', 'star'] },
        { emoji: '🌟', keywords: ['glänzend', 'stern', 'glow', 'star'] },
        { emoji: '✨', keywords: ['funkeln', 'sparkle'] },
        { emoji: '🔥', keywords: ['feuer', 'flamme', 'fire', 'flame'] },
        { emoji: '💧', keywords: ['tropfen', 'wasser', 'drop', 'water'] },
        { emoji: '🌈', keywords: ['regenbogen', 'rainbow'] }
    ],
    
    // Food & Drink (50+ emojis)
    food: [
        { emoji: '🍕', keywords: ['pizza', 'essen', 'food'] },
        { emoji: '🍔', keywords: ['burger', 'hamburger', 'essen', 'food'] },
        { emoji: '🍟', keywords: ['pommes', 'frites', 'fries'] },
        { emoji: '🌭', keywords: ['hotdog', 'würstchen', 'hot dog'] },
        { emoji: '🍿', keywords: ['popcorn', 'popcorn'] },
        { emoji: '🥓', keywords: ['speck', 'bacon'] },
        { emoji: '🥚', keywords: ['ei', 'egg'] },
        { emoji: '🍳', keywords: ['spiegelei', 'fried egg'] },
        { emoji: '🧇', keywords: ['waffel', 'waffle'] },
        { emoji: '🥞', keywords: ['pfannkuchen', 'pancakes'] },
        { emoji: '🧀', keywords: ['käse', 'cheese'] },
        { emoji: '🍖', keywords: ['fleisch', 'meat'] },
        { emoji: '🍗', keywords: ['hähnchen', 'chicken'] },
        { emoji: '🥩', keywords: ['steak', 'fleisch', 'steak', 'meat'] },
        { emoji: '🍞', keywords: ['brot', 'bread'] },
        { emoji: '🥐', keywords: ['croissant', 'croissant'] },
        { emoji: '🥖', keywords: ['baguette', 'baguette'] },
        { emoji: '🥨', keywords: ['brezel', 'pretzel'] },
        { emoji: '🥯', keywords: ['bagel', 'bagel'] },
        { emoji: '🌮', keywords: ['taco', 'taco'] },
        { emoji: '🌯', keywords: ['burrito', 'burrito'] },
        { emoji: '🥙', keywords: ['fladenbrot', 'pita'] },
        { emoji: '🍝', keywords: ['pasta', 'spaghetti', 'pasta'] },
        { emoji: '🍜', keywords: ['nudeln', 'suppe', 'noodles', 'soup'] },
        { emoji: '🍲', keywords: ['eintopf', 'stew'] },
        { emoji: '🍛', keywords: ['curry', 'reis', 'curry', 'rice'] },
        { emoji: '🍣', keywords: ['sushi', 'sushi'] },
        { emoji: '🍱', keywords: ['bento', 'bento'] },
        { emoji: '🍙', keywords: ['reisbällchen', 'rice ball'] },
        { emoji: '🍚', keywords: ['reis', 'rice'] },
        { emoji: '🍘', keywords: ['reiscracker', 'rice cracker'] },
        { emoji: '🍥', keywords: ['fischkuchen', 'fish cake'] },
        { emoji: '🥟', keywords: ['knödel', 'dumpling'] },
        { emoji: '🍦', keywords: ['eis', 'ice cream'] },
        { emoji: '🍧', keywords: ['eis', 'shaved ice'] },
        { emoji: '🍨', keywords: ['eis', 'ice cream'] },
        { emoji: '🍩', keywords: ['donut', 'donut'] },
        { emoji: '🍪', keywords: ['keks', 'cookie'] },
        { emoji: '🎂', keywords: ['kuchen', 'torte', 'cake'] },
        { emoji: '🍫', keywords: ['schokolade', 'chocolate'] },
        { emoji: '🍬', keywords: ['bonbon', 'süß', 'candy', 'sweet'] },
        { emoji: '🍭', keywords: ['lolli', 'lollipop'] },
        { emoji: '🍮', keywords: ['pudding', 'dessert', 'pudding'] },
        { emoji: '🍯', keywords: ['honig', 'honey'] },
        { emoji: '☕', keywords: ['kaffee', 'tee', 'coffee', 'tea'] },
        { emoji: '🍵', keywords: ['tee', 'tasse', 'tea', 'cup'] },
        { emoji: '🧃', keywords: ['saft', 'getränk', 'juice', 'drink'] },
        { emoji: '🥤', keywords: ['getränk', 'strohhalm', 'drink', 'straw'] },
        { emoji: '🍺', keywords: ['bier', 'beer'] },
        { emoji: '🍻', keywords: ['prost', 'bier', 'cheers', 'beer'] },
        { emoji: '🍷', keywords: ['wein', 'wine'] },
        { emoji: '🥂', keywords: ['sekt', 'prost', 'champagne', 'cheers'] },
        { emoji: '🍸', keywords: ['cocktail', 'cocktail'] },
        { emoji: '🍹', keywords: ['tropisch', 'cocktail', 'tropical', 'drink'] },
        { emoji: '🧋', keywords: ['bubble tea', 'tee', 'tea'] },
        { emoji: '🥛', keywords: ['milch', 'milk'] }
    ],
    
    // Sports & Activities (35+ emojis)
    sports: [
        { emoji: '⚽', keywords: ['fußball', 'ball', 'soccer', 'ball'] },
        { emoji: '🏀', keywords: ['basketball', 'basketball'] },
        { emoji: '🏈', keywords: ['american football', 'football'] },
        { emoji: '⚾', keywords: ['baseball', 'baseball'] },
        { emoji: '🎾', keywords: ['tennis', 'tennis'] },
        { emoji: '🏐', keywords: ['volleyball', 'volleyball'] },
        { emoji: '🏉', keywords: ['rugby', 'rugby'] },
        { emoji: '🎱', keywords: ['billard', 'pool'] },
        { emoji: '🏓', keywords: ['tischtennis', 'ping pong'] },
        { emoji: '🏸', keywords: ['badminton', 'badminton'] },
        { emoji: '🥅', keywords: ['tor', 'goal'] },
        { emoji: '🏒', keywords: ['hockey', 'hockey'] },
        { emoji: '🏑', keywords: ['feldhockey', 'field hockey'] },
        { emoji: '🏏', keywords: ['cricket', 'cricket'] },
        { emoji: '🥊', keywords: ['boxen', 'boxing'] },
        { emoji: '🥋', keywords: ['kampfsport', 'martial arts'] },
        { emoji: '⛳', keywords: ['golf', 'golf'] },
        { emoji: '🏹', keywords: ['bogenschießen', 'archery'] },
        { emoji: '🎣', keywords: ['angeln', 'fishing'] },
        { emoji: '🥏', keywords: ['frisbee', 'frisbee'] },
        { emoji: '🛹', keywords: ['skateboard', 'skateboard'] },
        { emoji: '🛼', keywords: ['rollschuh', 'roller skate'] },
        { emoji: '🏊', keywords: ['schwimmen', 'swimming'] },
        { emoji: '🏄', keywords: ['surfen', 'surfing'] },
        { emoji: '🚴', keywords: ['radfahren', 'cycling'] },
        { emoji: '🏃', keywords: ['laufen', 'running'] },
        { emoji: '⛷️', keywords: ['ski', 'skiing'] },
        { emoji: '🏂', keywords: ['snowboard', 'snowboarding'] },
        { emoji: '🤸', keywords: ['turnen', 'gymnastics'] },
        { emoji: '🏆', keywords: ['pokal', 'trophy', 'gewinner', 'winner'] },
        { emoji: '🥇', keywords: ['gold', 'medaille', 'gold', 'medal'] },
        { emoji: '🥈', keywords: ['silber', 'medaille', 'silver', 'medal'] },
        { emoji: '🥉', keywords: ['bronze', 'medaille', 'bronze', 'medal'] },
        { emoji: '🎯', keywords: ['ziel', 'treffer', 'target', 'bullseye'] },
        { emoji: '🎳', keywords: ['bowling', 'bowling'] },
        { emoji: '🎪', keywords: ['zirkus', 'circus'] },
        { emoji: '🎭', keywords: ['theater', 'maske', 'theater', 'mask'] },
        { emoji: '🎨', keywords: ['kunst', 'malen', 'art', 'paint'] },
        { emoji: '🎬', keywords: ['film', 'kino', 'movie', 'cinema'] },
        { emoji: '🎤', keywords: ['mikrofon', 'singen', 'microphone', 'sing'] },
        { emoji: '🎧', keywords: ['kopfhörer', 'musik', 'headphones', 'music'] },
        { emoji: '🎼', keywords: ['musik', 'noten', 'music', 'notes'] },
        { emoji: '🎹', keywords: ['klavier', 'piano'] },
        { emoji: '🎸', keywords: ['gitarre', 'guitar'] },
        { emoji: '🎺', keywords: ['trompete', 'trumpet'] },
        { emoji: '🎷', keywords: ['saxophon', 'saxophone'] },
        { emoji: '🥁', keywords: ['trommel', 'drum'] },
        { emoji: '🎮', keywords: ['spiel', 'videospiel', 'game', 'video game'] },
        { emoji: '🕹️', keywords: ['joystick', 'spiel', 'joystick', 'game'] },
        { emoji: '🎲', keywords: ['würfel', 'glück', 'dice', 'luck'] },
        { emoji: '🧩', keywords: ['puzzle', 'puzzle'] },
        { emoji: '♟️', keywords: ['schach', 'chess'] }
    ],
    
    // Travel & Places (45+ emojis)
    travel: [
        { emoji: '🚗', keywords: ['auto', 'wagen', 'car', 'vehicle'] },
        { emoji: '🚕', keywords: ['taxi', 'taxi'] },
        { emoji: '🚙', keywords: ['jeep', 'auto', 'jeep', 'car'] },
        { emoji: '🚌', keywords: ['bus', 'bus'] },
        { emoji: '🚎', keywords: ['trolley', 'bus'] },
        { emoji: '🏎️', keywords: ['rennauto', 'sportwagen', 'race car', 'sports car'] },
        { emoji: '🚓', keywords: ['polizei', 'polizeiauto', 'police', 'car'] },
        { emoji: '🚑', keywords: ['krankenwagen', 'ambulance'] },
        { emoji: '🚒', keywords: ['feuerwehr', 'fire truck'] },
        { emoji: '✈️', keywords: ['flugzeug', 'fliegen', 'plane', 'fly'] },
        { emoji: '🚀', keywords: ['rakete', 'weltraum', 'rocket', 'space'] },
        { emoji: '🛸', keywords: ['ufo', 'fliegende untertasse', 'ufo'] },
        { emoji: '🚁', keywords: ['helikopter', 'helicopter'] },
        { emoji: '🚂', keywords: ['zug', 'lokomotive', 'train', 'locomotive'] },
        { emoji: '🚃', keywords: ['bahn', 'zug', 'train'] },
        { emoji: '🚄', keywords: ['hochgeschwindigkeitszug', 'bullet train'] },
        { emoji: '🚢', keywords: ['schiff', 'fähre', 'ship', 'ferry'] },
        { emoji: '⛵', keywords: ['segeln', 'boot', 'sailing', 'boat'] },
        { emoji: '🛶', keywords: ['kanu', 'canoe'] },
        { emoji: '🏠', keywords: ['haus', 'zuhause', 'house', 'home'] },
        { emoji: '🏡', keywords: ['haus', 'garten', 'house', 'garden'] },
        { emoji: '🏢', keywords: ['gebäude', 'büro', 'building', 'office'] },
        { emoji: '🏣', keywords: ['post', 'postamt', 'post office'] },
        { emoji: '🏥', keywords: ['krankenhaus', 'hospital'] },
        { emoji: '🏦', keywords: ['bank', 'bank'] },
        { emoji: '🏨', keywords: ['hotel', 'hotel'] },
        { emoji: '🏩', keywords: ['liebeshotel', 'love hotel'] },
        { emoji: '🏪', keywords: ['laden', 'shop', 'store'] },
        { emoji: '🏫', keywords: ['schule', 'school'] },
        { emoji: '🏯', keywords: ['burg', 'japan', 'castle', 'japan'] },
        { emoji: '⛪', keywords: ['kirche', 'church'] },
        { emoji: '🕌', keywords: ['moschee', 'mosque'] },
        { emoji: '🕍', keywords: ['synagoge', 'synagogue'] },
        { emoji: '🗼', keywords: ['turm', 'tokio', 'tower', 'tokyo'] },
        { emoji: '🗽', keywords: ['freiheitsstatue', 'statue of liberty'] },
        { emoji: '🗾', keywords: ['japan', 'karte', 'japan', 'map'] },
        { emoji: '🌋', keywords: ['vulkan', 'volcano'] },
        { emoji: '⛰️', keywords: ['berg', 'mountain'] },
        { emoji: '🏔️', keywords: ['schnee', 'berg', 'snow', 'mountain'] },
        { emoji: '🗻', keywords: ['fuji', 'berg', 'mount fuji'] },
        { emoji: '🏕️', keywords: ['camping', 'zelt', 'camping', 'tent'] },
        { emoji: '🏖️', keywords: ['strand', 'beach'] },
        { emoji: '⛱️', keywords: ['sonnenschirm', 'strand', 'umbrella', 'beach'] },
        { emoji: '🌅', keywords: ['sonnenaufgang', 'sunrise'] },
        { emoji: '🌄', keywords: ['berg', 'sonnenaufgang', 'mountain', 'sunrise'] },
        { emoji: '🌠', keywords: ['schießender stern', 'shooting star'] }
    ],
    
    // Objects & Tech (40+ emojis)
    objects: [
        { emoji: '📱', keywords: ['handy', 'telefon', 'phone', 'mobile'] },
        { emoji: '💻', keywords: ['laptop', 'computer', 'laptop', 'computer'] },
        { emoji: '🖥️', keywords: ['computer', 'monitor', 'computer', 'desktop'] },
        { emoji: '⌨️', keywords: ['tastatur', 'keyboard'] },
        { emoji: '🖱️', keywords: ['maus', 'mouse'] },
        { emoji: '🖨️', keywords: ['drucker', 'printer'] },
        { emoji: '📷', keywords: ['kamera', 'foto', 'camera', 'photo'] },
        { emoji: '📸', keywords: ['blitz', 'foto', 'flash', 'photo'] },
        { emoji: '📹', keywords: ['videokamera', 'video camera'] },
        { emoji: '📺', keywords: ['fernsehen', 'tv', 'television'] },
        { emoji: '📻', keywords: ['radio', 'radio'] },
        { emoji: '🔔', keywords: ['glocke', 'benachrichtigung', 'bell', 'notification'] },
        { emoji: '⏰', keywords: ['wecker', 'uhr', 'alarm', 'clock'] },
        { emoji: '⌛', keywords: ['sanduhr', 'zeit', 'hourglass', 'time'] },
        { emoji: '📡', keywords: ['satellit', 'antenne', 'satellite'] },
        { emoji: '💡', keywords: ['lampe', 'idee', 'light', 'idea'] },
        { emoji: '🔦', keywords: ['taschenlampe', 'flashlight'] },
        { emoji: '🔋', keywords: ['batterie', 'battery'] },
        { emoji: '🔌', keywords: ['stecker', 'plug'] },
        { emoji: '💰', keywords: ['geldbeutel', 'geld', 'money bag', 'money'] },
        { emoji: '💵', keywords: ['dollar', 'geld', 'dollar', 'money'] },
        { emoji: '💴', keywords: ['yen', 'geld', 'yen'] },
        { emoji: '💶', keywords: ['euro', 'geld', 'euro'] },
        { emoji: '💷', keywords: ['pfund', 'geld', 'pound'] },
        { emoji: '✉️', keywords: ['brief', 'email', 'letter', 'email'] },
        { emoji: '📧', keywords: ['email', 'e-mail'] },
        { emoji: '📬', keywords: ['postkasten', 'mailbox'] },
        { emoji: '📭', keywords: ['postkasten', 'leer', 'mailbox', 'empty'] },
        { emoji: '📮', keywords: ['briefkasten', 'postbox'] },
        { emoji: '📝', keywords: ['notiz', 'schreiben', 'note', 'write'] },
        { emoji: '📁', keywords: ['ordner', 'folder'] },
        { emoji: '📂', keywords: ['ordner', 'offen', 'folder', 'open'] },
        { emoji: '📅', keywords: ['kalender', 'datum', 'calendar', 'date'] },
        { emoji: '📆', keywords: ['kalender', 'ablauf', 'calendar'] },
        { emoji: '🔒', keywords: ['schloss', 'verschlossen', 'lock', 'locked'] },
        { emoji: '🔓', keywords: ['entsperrt', 'unlocked'] },
        { emoji: '🔑', keywords: ['schlüssel', 'key'] },
        { emoji: '🔨', keywords: ['hammer', 'hammer'] },
        { emoji: '🪓', keywords: ['axt', 'axe'] },
        { emoji: '⚙️', keywords: ['einstellungen', 'zahnrad', 'settings', 'gear'] },
        { emoji: '🔧', keywords: ['werkzeug', 'reparieren', 'wrench', 'repair'] },
        { emoji: '🧲', keywords: ['magnet', 'magnet'] },
        { emoji: '🔬', keywords: ['mikroskop', 'wissenschaft', 'microscope', 'science'] },
        { emoji: '🔭', keywords: ['teleskop', 'telescope'] },
        { emoji: '💊', keywords: ['pille', 'medizin', 'pill', 'medicine'] },
        { emoji: '💉', keywords: ['spritze', 'impfung', 'syringe', 'vaccine'] },
        { emoji: '🩹', keywords: ['pflaster', 'band aid'] },
        { emoji: '🛒', keywords: ['einkaufswagen', 'shopping cart'] }
    ],
    
    // Symbols & Signs (35+ emojis)
    symbols: [
        { emoji: '✅', keywords: ['richtig', 'erledigt', 'check', 'done'] },
        { emoji: '✔️', keywords: ['haken', 'richtig', 'check', 'correct'] },
        { emoji: '❌', keywords: ['falsch', 'kreuz', 'wrong', 'cross'] },
        { emoji: '❓', keywords: ['frage', 'question'] },
        { emoji: '❗', keywords: ['ausrufezeichen', 'wichtig', 'exclamation', 'important'] },
        { emoji: '‼️', keywords: ['doppelt', 'ausrufezeichen', 'double exclamation'] },
        { emoji: '💯', keywords: ['hundert', 'perfekt', 'hundred', 'perfect'] },
        { emoji: '🔴', keywords: ['rot', 'kreis', 'red', 'circle'] },
        { emoji: '🟠', keywords: ['orange', 'orange'] },
        { emoji: '🟡', keywords: ['gelb', 'yellow'] },
        { emoji: '🟢', keywords: ['grün', 'green'] },
        { emoji: '🔵', keywords: ['blau', 'blue'] },
        { emoji: '🟣', keywords: ['lila', 'purple'] },
        { emoji: '🟤', keywords: ['braun', 'brown'] },
        { emoji: '⚫', keywords: ['schwarz', 'black'] },
        { emoji: '⚪', keywords: ['weiß', 'white'] },
        { emoji: '🟥', keywords: ['quadrat', 'rot', 'square', 'red'] },
        { emoji: '🟧', keywords: ['quadrat', 'orange', 'square', 'orange'] },
        { emoji: '🟨', keywords: ['quadrat', 'gelb', 'square', 'yellow'] },
        { emoji: '🟩', keywords: ['quadrat', 'grün', 'square', 'green'] },
        { emoji: '🟦', keywords: ['quadrat', 'blau', 'square', 'blue'] },
        { emoji: '🟪', keywords: ['quadrat', 'lila', 'square', 'purple'] },
        { emoji: '⬛', keywords: ['schwarz', 'groß', 'black', 'large'] },
        { emoji: '⬜', keywords: ['weiß', 'groß', 'white', 'large'] },
        { emoji: '🔶', keywords: ['orange', 'diamant', 'orange', 'diamond'] },
        { emoji: '🔷', keywords: ['blau', 'diamant', 'blue', 'diamond'] },
        { emoji: '🔸', keywords: ['klein', 'orange', 'small', 'orange'] },
        { emoji: '🔹', keywords: ['klein', 'blau', 'small', 'blue'] },
        { emoji: '🔺', keywords: ['dreieck', 'rot', 'triangle', 'red'] },
        { emoji: '🔻', keywords: ['dreieck', 'runter', 'triangle', 'down'] },
        { emoji: '💠', keywords: ['diamant', 'punkt', 'diamond', 'dot'] },
        { emoji: '🔘', keywords: ['radio', 'knopf', 'radio', 'button'] },
        { emoji: '🔳', keywords: ['quadrat', 'rahmen', 'square', 'outline'] },
        { emoji: '🔲', keywords: ['quadrat', 'voll', 'square', 'filled'] },
        { emoji: '➡️', keywords: ['pfeil', 'rechts', 'arrow', 'right'] },
        { emoji: '⬅️', keywords: ['pfeil', 'links', 'arrow', 'left'] },
        { emoji: '⬆️', keywords: ['pfeil', 'oben', 'arrow', 'up'] },
        { emoji: '⬇️', keywords: ['pfeil', 'unten', 'arrow', 'down'] },
        { emoji: '🔄', keywords: ['recyclen', 'wiederholen', 'recycle', 'repeat'] },
        { emoji: '♻️', keywords: ['recyclen', 'umwelt', 'recycle', 'environment'] },
        { emoji: '✳️', keywords: ['stern', 'asterisk', 'star'] },
        { emoji: '❇️', keywords: ['funkeln', 'sparkle'] },
        { emoji: '🆗', keywords: ['ok', 'ok'] },
        { emoji: '🆒', keywords: ['cool', 'cool'] },
        { emoji: '🆕', keywords: ['neu', 'new'] },
        { emoji: '🆓', keywords: ['kostenlos', 'free'] },
        { emoji: '0️⃣', keywords: ['null', 'zahl', 'zero', 'number'] },
        { emoji: '1️⃣', keywords: ['eins', 'eins', 'one', 'number'] },
        { emoji: '2️⃣', keywords: ['zwei', 'two', 'number'] },
        { emoji: '3️⃣', keywords: ['drei', 'three', 'number'] },
        { emoji: '4️⃣', keywords: ['vier', 'four', 'number'] },
        { emoji: '5️⃣', keywords: ['fünf', 'five', 'number'] },
        { emoji: '☀️', keywords: ['sonne', 'sonnig', 'sun', 'sunny'] },
        { emoji: '🌤️', keywords: ['wolke', 'sonne', 'cloud', 'sun'] },
        { emoji: '⛅', keywords: ['wolke', 'sonne', 'cloud', 'sun'] },
        { emoji: '🌧️', keywords: ['regen', 'regnerisch', 'rain', 'rainy'] },
        { emoji: '❄️', keywords: ['schnee', 'eis', 'snow', 'ice'] },
        { emoji: '💨', keywords: ['wind', 'wind'] },
        { emoji: '🌪️', keywords: ['wirbelsturm', 'tornado'] },
        { emoji: '☁️', keywords: ['wolke', 'cloud'] }
    ],
    
    // Celebration & Events (30+ emojis)
    celebration: [
        { emoji: '🎉', keywords: ['party', 'feier', 'konfetti', 'party', 'celebrate', 'confetti'] },
        { emoji: '🎊', keywords: ['feier', 'ball', 'celebration', 'ball'] },
        { emoji: '🎈', keywords: ['ballon', 'geburtstag', 'balloon', 'birthday'] },
        { emoji: '🎁', keywords: ['geschenk', 'present', 'gift'] },
        { emoji: '🎀', keywords: ['schleife', 'ribbon'] },
        { emoji: '🎗️', keywords: ['erinnerung', 'reminder'] },
        { emoji: '🎖️', keywords: ['medaille', 'militär', 'medal', 'military'] },
        { emoji: '🏅', keywords: ['medaille', 'sport', 'medal', 'sport'] },
        { emoji: '🎄', keywords: ['weihnachten', 'baum', 'christmas', 'tree'] },
        { emoji: '🎃', keywords: ['kürbis', 'halloween', 'pumpkin', 'halloween'] },
        { emoji: '🎆', keywords: ['feuerwerk', 'silvester', 'fireworks'] },
        { emoji: '🎇', keywords: ['feuerwerk', 'funke', 'sparkler'] },
        { emoji: '🧨', keywords: ['knallkörper', 'firecracker'] },
        { emoji: '✨', keywords: ['funkeln', 'sparkle'] },
        { emoji: '🎋', keywords: ['tanabata', 'baum', 'tanabata', 'tree'] },
        { emoji: '🎍', keywords: ['neujahr', 'japan', 'new year', 'japan'] },
        { emoji: '🎎', keywords: ['puppe', 'japan', 'doll', 'japan'] },
        { emoji: '🎏', keywords: ['karpfen', 'flagge', 'carp', 'flag'] },
        { emoji: '🎐', keywords: ['windspiel', 'wind chime'] },
        { emoji: '🎑', keywords: ['mond', 'ernte', 'moon', 'harvest'] },
        { emoji: '🧧', keywords: ['rot', 'umschlag', 'red envelope', 'lucky'] },
        { emoji: '🎫', keywords: ['ticket', 'karte', 'ticket'] },
        { emoji: '🎟️', keywords: ['eintrittskarte', 'admission ticket'] },
        { emoji: '🪅', keywords: ['pinata', 'party', 'pinata'] },
        { emoji: '🪆', keywords: ['matroschka', 'puppe', 'nesting doll'] },
        { emoji: '🎠', keywords: ['karussell', 'carousel'] },
        { emoji: '🎡', keywords: ['riesenrad', 'ferris wheel'] },
        { emoji: '🎢', keywords: ['achterbahn', 'roller coaster'] }
    ]
};

// Flatten all emojis into a single array
const ALL_EMOJIS = Object.values(EMOJI_DATA).flat();

class CustomEmojiPicker {
    constructor(targetInput) {
        this.targetInput = targetInput;
        this.container = null;
        this.searchInput = null;
        this.emojiGrid = null;
        this.isOpen = false;
        this.currentCategory = 'all';
        this.onToggleCallback = null; // Callback for icon toggle
        
        // Lazy loading properties
        this.allEmojisToShow = []; // All emojis to be displayed
        this.loadedCount = 0; // Number of emojis already loaded
        this.batchSize = 100; // Number of emojis to load per batch
        this.isLoading = false; // Prevent multiple simultaneous loads
        
        this.init();
    }
    
    init() {
        this.createPicker();
        this.attachEventListeners();
    }
    
    createPicker() {
        // Create main container (Always Dark Theme)
        this.container = document.createElement('div');
        this.container.className = 'custom-emoji-picker';
        this.container.style.cssText = `
            position: fixed;
            background: #1f1f1f;
            border: 1px solid #333;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            width: 350px;
            max-height: 450px;
            display: none;
            flex-direction: column;
            z-index: 10000;
        `;
        
        // Header with search (Dark Theme)
        const header = document.createElement('div');
        header.style.cssText = `
            padding: 12px;
            border-bottom: 1px solid #333;
            display: flex;
            align-items: center;
            gap: 8px;
        `;
        
        // Search icon
        const searchIcon = document.createElement('span');
        searchIcon.innerHTML = '🔍';
        searchIcon.style.fontSize = '18px';
        
        // Search input (Dark Theme)
        this.searchInput = document.createElement('input');
        this.searchInput.type = 'text';
        this.searchInput.placeholder = 'Emojis suchen / Search... (z.B. Liebe, love, Lachen)';
        this.searchInput.style.cssText = `
            flex: 1;
            border: none;
            outline: none;
            font-size: 14px;
            background: #2a2a2a;
            color: #fff;
            border-radius: 6px;
            padding: 6px 10px;
        `;
        
        // Close button (Dark Theme)
        const closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.innerHTML = '✕';
        closeBtn.style.cssText = `
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #aaa;
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        `;
        closeBtn.onclick = () => this.close();
        
        header.appendChild(searchIcon);
        header.appendChild(this.searchInput);
        header.appendChild(closeBtn);
        
        // Category tabs (Dark Theme)
        const categoryTabs = document.createElement('div');
        categoryTabs.style.cssText = `
            display: flex;
            gap: 2px;
            padding: 8px 6px;
            border-bottom: 1px solid #333;
            justify-content: space-between;
            flex-wrap: nowrap;
            overflow-x: hidden;
            width: 100%;
        `;
        
        const categories = [
            { id: 'all', emoji: '🎯', name: 'Alle' },
            { id: 'smileys', emoji: '😀', name: 'Smileys' },
            { id: 'gestures', emoji: '👍', name: 'Gesten' },
            { id: 'hearts', emoji: '❤️', name: 'Herzen' },
            { id: 'sad', emoji: '😢', name: 'Traurig' },
            { id: 'animals', emoji: '🐶', name: 'Tiere' },
            { id: 'food', emoji: '🍕', name: 'Essen' },
            { id: 'sports', emoji: '⚽', name: 'Sport' },
            { id: 'travel', emoji: '✈️', name: 'Reisen' },
            { id: 'objects', emoji: '📱', name: 'Objekte' },
            { id: 'symbols', emoji: '✅', name: 'Symbole' },
            { id: 'celebration', emoji: '🎉', name: 'Feier' }
        ];
        
        // Store category buttons for later reference
        this.categoryButtons = {};
        
        categories.forEach(cat => {
            const tabWrapper = document.createElement('div');
            tabWrapper.style.cssText = `
                position: relative;
                display: flex;
                flex-direction: column;
                align-items: center;
                flex-shrink: 0;
            `;
            
            const tab = document.createElement('button');
            tab.type = 'button';
            tab.innerHTML = cat.emoji;
            tab.title = cat.name;
            tab.dataset.categoryId = cat.id;
            tab.style.cssText = `
                background: none;
                border: none;
                font-size: 20px;
                cursor: pointer;
                padding: 3px;
                border-radius: 6px;
                transition: background 0.2s;
                min-width: 32px;
                display: flex;
                align-items: center;
                justify-content: center;
            `;
            
            // Active indicator (purple line)
            const indicator = document.createElement('div');
            indicator.className = 'category-indicator';
            indicator.style.cssText = `
                width: 100%;
                height: 3px;
                background: #8b5cf6;
                border-radius: 2px;
                margin-top: 2px;
                opacity: 0;
                transition: opacity 0.2s;
            `;
            
            tab.onclick = () => {
                this.filterByCategory(cat.id);
                this.setActiveCategory(cat.id);
            };
            
            tab.onmouseenter = function() {
                this.style.background = '#2a2a2a';
            };
            tab.onmouseleave = function() {
                this.style.background = 'none';
            };
            
            tabWrapper.appendChild(tab);
            tabWrapper.appendChild(indicator);
            categoryTabs.appendChild(tabWrapper);
            
            // Store reference for active state management
            this.categoryButtons[cat.id] = { button: tab, indicator: indicator };
        });
        
        // Emoji grid
        this.emojiGrid = document.createElement('div');
        this.emojiGrid.style.cssText = `
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 4px;
            padding: 10px;
            overflow-y: auto;
            overflow-x: hidden;
            max-height: 320px;
        `;
        
        this.container.appendChild(header);
        this.container.appendChild(categoryTabs);
        this.container.appendChild(this.emojiGrid);
        
        // Append to document body for fixed positioning
        document.body.appendChild(this.container);
        
        // Add scroll event listener for lazy loading
        this.emojiGrid.addEventListener('scroll', () => {
            // Check if user has scrolled near the bottom
            const scrollTop = this.emojiGrid.scrollTop;
            const scrollHeight = this.emojiGrid.scrollHeight;
            const clientHeight = this.emojiGrid.clientHeight;
            
            // Load more when 100px from bottom
            if (scrollTop + clientHeight >= scrollHeight - 100) {
                if (this.loadedCount < this.allEmojisToShow.length) {
                    this.loadMoreEmojis();
                }
            }
        });
        
        // Render all emojis initially (first batch only)
        this.renderEmojis(ALL_EMOJIS);
    }
    
    renderEmojis(emojis, reset = true) {
        if (reset) {
            // Reset for new emoji set
            this.emojiGrid.innerHTML = '';
            this.allEmojisToShow = emojis;
            this.loadedCount = 0;
        }
        
        if (emojis.length === 0 && reset) {
            const noResults = document.createElement('div');
            noResults.textContent = 'Keine Emojis gefunden / No emojis found';
            noResults.style.cssText = `
                grid-column: 1 / -1;
                text-align: center;
                padding: 20px;
                color: #666;
            `;
            this.emojiGrid.appendChild(noResults);
            return;
        }
        
        // Load initial batch
        this.loadMoreEmojis();
    }
    
    loadMoreEmojis() {
        if (this.isLoading) return;
        
        this.isLoading = true;
        
        // Calculate how many emojis to load
        const startIndex = this.loadedCount;
        const endIndex = Math.min(startIndex + this.batchSize, this.allEmojisToShow.length);
        const emojisToLoad = this.allEmojisToShow.slice(startIndex, endIndex);
        
        // Render the batch
        emojisToLoad.forEach(item => {
            const emojiBtn = document.createElement('button');
            emojiBtn.type = 'button';
            emojiBtn.textContent = item.emoji;
            emojiBtn.title = item.keywords.slice(0, 3).join(', ');
            emojiBtn.style.cssText = `
                background: none;
                border: none;
                font-size: 26px;
                cursor: pointer;
                padding: 6px;
                border-radius: 6px;
                transition: all 0.2s;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 100%;
                aspect-ratio: 1;
            `;
            
            emojiBtn.onmouseenter = function() {
                this.style.background = '#2a2a2a';
                this.style.transform = 'scale(1.2)';
            };
            emojiBtn.onmouseleave = function() {
                this.style.background = 'none';
                this.style.transform = 'scale(1)';
            };
            
            emojiBtn.onclick = () => this.insertEmoji(item.emoji);
            
            this.emojiGrid.appendChild(emojiBtn);
        });
        
        this.loadedCount = endIndex;
        this.isLoading = false;
    }
    
    setActiveCategory(categoryId) {
        // Remove active state from all categories
        Object.keys(this.categoryButtons).forEach(id => {
            const { indicator } = this.categoryButtons[id];
            indicator.style.opacity = '0';
        });
        
        // Set active state for selected category
        if (this.categoryButtons[categoryId]) {
            const { indicator } = this.categoryButtons[categoryId];
            indicator.style.opacity = '1';
        }
    }
    
    filterByCategory(categoryId) {
        this.currentCategory = categoryId;
        this.searchInput.value = '';
        
        // Reset scroll position
        this.emojiGrid.scrollTop = 0;
        
        // Update active indicator
        this.setActiveCategory(categoryId);
        
        if (categoryId === 'all') {
            this.renderEmojis(ALL_EMOJIS, true);
        } else {
            this.renderEmojis(EMOJI_DATA[categoryId] || [], true);
        }
    }
    
    search(query) {
        if (!query.trim()) {
            this.filterByCategory(this.currentCategory);
            return;
        }
        
        // Reset scroll position
        this.emojiGrid.scrollTop = 0;
        
        // Clear active category indicator during search
        Object.keys(this.categoryButtons).forEach(id => {
            const { indicator } = this.categoryButtons[id];
            indicator.style.opacity = '0';
        });
        
        const searchTerm = query.toLowerCase();
        const results = ALL_EMOJIS.filter(item => 
            item.keywords.some(keyword => keyword.toLowerCase().includes(searchTerm))
        );
        
        this.renderEmojis(results, true);
    }
    
    insertEmoji(emoji) {
        const input = this.targetInput;
        const start = input.selectionStart;
        const end = input.selectionEnd;
        const currentValue = input.value;
        
        // Insert emoji at cursor position
        const newValue = currentValue.substring(0, start) + emoji + currentValue.substring(end);
        input.value = newValue;
        
        // Move cursor after emoji
        const newPosition = start + emoji.length;
        input.setSelectionRange(newPosition, newPosition);
        
        // Focus input
        input.focus();
        
        // Trigger input event for any listeners
        input.dispatchEvent(new Event('input', { bubbles: true }));
        
        // Don't close picker - allow multiple selections
    }
    
    attachEventListeners() {
        // Search input
        this.searchInput.addEventListener('input', (e) => {
            this.search(e.target.value);
        });
        
        // Click outside to close
        document.addEventListener('click', (e) => {
            if (this.isOpen && !this.container.contains(e.target) && e.target !== this.targetInput) {
                const emojiButton = e.target.closest('.emoji-picker-button');
                if (!emojiButton) {
                    this.close();
                }
            }
        });
        
        // Escape key to close
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.close();
            }
        });
        
        // Reposition on window resize or scroll
        window.addEventListener('resize', () => {
            if (this.isOpen) {
                this.positionPicker();
            }
        });
        
        window.addEventListener('scroll', () => {
            if (this.isOpen) {
                this.positionPicker();
            }
        }, true);
    }
    
    open() {
        this.container.style.display = 'flex';
        this.isOpen = true;
        
        // Position picker intelligently based on input position and available space
        this.positionPicker();
        
        // Set default active category
        this.setActiveCategory('all');
        
        this.searchInput.focus();
        
        // Trigger icon toggle callback
        if (this.onToggleCallback) {
            this.onToggleCallback(true);
        }
    }
    
    positionPicker() {
        // Find emoji button position (not textarea, but the emoji button)
        const emojiButton = this.targetInput.parentElement.querySelector('.emoji-picker-button');
        const buttonRect = emojiButton ? emojiButton.getBoundingClientRect() : this.targetInput.getBoundingClientRect();
        
        const pickerHeight = 450; // max height
        const pickerWidth = 350;
        const spacing = 10;
        
        const viewportHeight = window.innerHeight;
        const viewportWidth = window.innerWidth;
        
        // Calculate space above button
        const spaceAbove = buttonRect.top;
        const spaceBelow = viewportHeight - buttonRect.bottom;
        
        let top, left;
        
        // Prefer to show ABOVE the emoji button
        if (spaceAbove >= pickerHeight + spacing) {
            // Show above emoji button
            top = buttonRect.top - pickerHeight - spacing;
        } else if (spaceBelow >= pickerHeight + spacing) {
            // If not enough space above, show below
            top = buttonRect.bottom + spacing;
        } else {
            // If neither has enough space, show where there's more space
            if (spaceAbove > spaceBelow) {
                top = spacing; // Top of viewport
            } else {
                top = viewportHeight - pickerHeight - spacing; // Bottom of viewport
            }
        }
        
        // Ensure picker doesn't go above viewport
        if (top < spacing) {
            top = spacing;
        }
        
        // Ensure picker doesn't go below viewport
        if (top + pickerHeight > viewportHeight - spacing) {
            top = viewportHeight - pickerHeight - spacing;
        }
        
        // Horizontal positioning - try to align with button/input
        left = buttonRect.left - (pickerWidth / 2) + (buttonRect.width / 2);
        
        // Ensure picker doesn't overflow right side
        if (left + pickerWidth > viewportWidth - spacing) {
            left = viewportWidth - pickerWidth - spacing;
        }
        
        // Ensure picker doesn't overflow left side
        if (left < spacing) {
            left = spacing;
        }
        
        this.container.style.top = top + 'px';
        this.container.style.left = left + 'px';
    }
    
    close() {
        this.container.style.display = 'none';
        this.isOpen = false;
        this.searchInput.value = '';
        
        // Reset scroll position
        this.emojiGrid.scrollTop = 0;
        
        this.filterByCategory('all');
        
        // Trigger icon toggle callback
        if (this.onToggleCallback) {
            this.onToggleCallback(false);
        }
    }
    
    toggle() {
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }
    }
}

// Global function to initialize custom emoji picker
window.initCustomEmojiPicker = function(selector) {
    const textarea = typeof selector === 'string' ? document.querySelector(selector) : selector;
    
    if (!textarea) {
        console.warn('Textarea not found for selector:', selector);
        return null;
    }
    
    // Check if already initialized
    if (textarea.dataset.customEmojiPickerInit === 'true') {
        return textarea.customEmojiPicker;
    }
    
    // Mark as initialized
    textarea.dataset.customEmojiPickerInit = 'true';
    
    // Create wrapper if it doesn't exist
    let wrapper = textarea.parentElement;
    if (!wrapper.classList.contains('emoji-picker-wrapper')) {
        const newWrapper = document.createElement('div');
        newWrapper.className = 'emoji-picker-wrapper';
        newWrapper.style.position = 'relative';
        newWrapper.style.display = 'block';
        textarea.parentNode.insertBefore(newWrapper, textarea);
        newWrapper.appendChild(textarea);
        wrapper = newWrapper;
        
        // Add padding to textarea for emoji button
        if (!textarea.style.paddingRight) {
            textarea.style.paddingRight = '45px';
        }
    }
    
    // SVG icons for emoji picker button
    const emojiSVG = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17.5,12 C20.5375661,12 23,14.4624339 23,17.5 C23,20.5375661 20.5375661,23 17.5,23 C14.4624339,23 12,20.5375661 12,17.5 C12,14.4624339 14.4624339,12 17.5,12 Z M12.0000002,1.99896738 C17.523704,1.99896738 22.0015507,6.47681407 22.0015507,12.0005179 C22.0015507,12.2637452 21.9913819,12.5245975 21.9714157,12.7827034 C21.5335438,12.3671164 21.0376367,12.012094 20.4972374,11.7307716 C20.3551544,7.16057357 16.6051843,3.49896738 12.0000002,3.49896738 C7.30472352,3.49896738 3.49844971,7.30524119 3.49844971,12.0005179 C3.49844971,16.6060394 7.16059249,20.3562216 11.7317296,20.4979161 C12.0124658,21.0381559 12.3673338,21.5337732 12.7825138,21.9716342 C12.5247521,21.9918733 12.2635668,22.0020684 12.0000002,22.0020684 C6.47629639,22.0020684 1.99844971,17.5242217 1.99844971,12.0005179 C1.99844971,6.47681407 6.47629639,1.99896738 12.0000002,1.99896738 Z M17.5,13.9992349 L17.4101244,14.0072906 C17.2060313,14.0443345 17.0450996,14.2052662 17.0080557,14.4093593 L17,14.4992349 L16.9996498,16.9992349 L14.4976498,17 L14.4077742,17.0080557 C14.2036811,17.0450996 14.0427494,17.2060313 14.0057055,17.4101244 L13.9976498,17.5 L14.0057055,17.5898756 C14.0427494,17.7939687 14.2036811,17.9549004 14.4077742,17.9919443 L14.4976498,18 L17.0006498,17.9992349 L17.0011076,20.5034847 L17.0091633,20.5933603 C17.0462073,20.7974534 17.207139,20.9583851 17.411232,20.995429 L17.5011076,21.0034847 L17.5909833,20.995429 C17.7950763,20.9583851 17.956008,20.7974534 17.993052,20.5933603 L18.0011076,20.5034847 L18.0006498,17.9992349 L20.5045655,18 L20.5944411,17.9919443 C20.7985342,17.9549004 20.9594659,17.7939687 20.9965098,17.5898756 L21.0045655,17.5 L20.9965098,17.4101244 C20.9594659,17.2060313 20.7985342,17.0450996 20.5944411,17.0080557 L20.5045655,17 L17.9996498,16.9992349 L18,14.4992349 L17.9919443,14.4093593 C17.9549004,14.2052662 17.7939687,14.0443345 17.5898756,14.0072906 L17.5,13.9992349 Z M8.46174078,14.7838355 C9.12309331,15.6232213 10.0524954,16.1974014 11.0917655,16.4103066 C11.0312056,16.7638158 11,17.1282637 11,17.5 C11,17.6408778 11.0044818,17.7807089 11.0133105,17.9193584 C9.53812034,17.6766509 8.21128537,16.8896809 7.28351576,15.7121597 C7.02716611,15.3868018 7.08310832,14.9152347 7.40846617,14.6588851 C7.73382403,14.4025354 8.20539113,14.4584777 8.46174078,14.7838355 Z M9.00044779,8.75115873 C9.69041108,8.75115873 10.2497368,9.3104845 10.2497368,10.0004478 C10.2497368,10.6904111 9.69041108,11.2497368 9.00044779,11.2497368 C8.3104845,11.2497368 7.75115873,10.6904111 7.75115873,10.0004478 C7.75115873,9.3104845 8.3104845,8.75115873 9.00044779,8.75115873 Z M15.0004478,8.75115873 C15.6904111,8.75115873 16.2497368,9.3104845 16.2497368,10.0004478 C16.2497368,10.6904111 15.6904111,11.2497368 15.0004478,11.2497368 C14.3104845,11.2497368 13.7511587,10.6904111 13.7511587,10.0004478 C13.7511587,9.3104845 14.3104845,8.75115873 15.0004478,8.75115873 Z" fill="currentColor"/></svg>`;
    
    const closeSVG = `<svg width="20" height="20" viewBox="0 0 455 455" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M227.5,0C101.761,0,0,101.75,0,227.5C0,353.239,101.75,455,227.5,455C353.239,455,455,353.25,455,227.5C455.001,101.761,353.251,0,227.5,0z M227.5,425.001c-108.902,0-197.5-88.599-197.5-197.5S118.599,30,227.5,30S425,118.599,425,227.5S336.402,425.001,227.5,425.001z"/><path d="M321.366,133.635c-17.587-17.588-46.051-17.589-63.64,0L227.5,163.86l-30.226-30.225c-17.588-17.588-46.051-17.589-63.64,0c-17.544,17.545-17.544,46.094,0,63.64L163.86,227.5l-30.226,30.226c-17.544,17.545-17.544,46.094,0,63.64c17.585,17.586,46.052,17.589,63.64,0l30.226-30.225l30.226,30.225c17.585,17.586,46.052,17.589,63.64,0c17.544-17.545,17.544-46.094,0-63.64L291.141,227.5l30.226-30.226C338.911,179.729,338.911,151.181,321.366,133.635z M300.153,176.062l-40.832,40.832c-2.813,2.813-4.394,6.628-4.394,10.606c0,3.979,1.581,7.793,4.394,10.606l40.832,40.832c5.849,5.849,5.849,15.365,0,21.214c-5.862,5.862-15.351,5.863-21.214,0l-40.832-40.832c-2.929-2.929-6.768-4.394-10.606-4.394s-7.678,1.464-10.606,4.394l-40.832,40.832c-5.861,5.861-15.351,5.863-21.213,0c-5.849-5.849-5.849-15.365,0-21.214l40.832-40.832c2.813-2.813,4.394-6.628,4.394-10.606c0-3.978-1.581-7.793-4.394-10.606l-40.832-40.832c-5.849-5.849-5.849-15.365,0-21.214c5.864-5.863,15.35-5.863,21.214,0l40.832,40.832c5.857,5.858,15.355,5.858,21.213,0l40.832-40.832c5.863-5.862,15.35-5.863,21.213,0C306.001,160.697,306.001,170.213,300.153,176.062z"/></svg>`;
    
    // Create emoji button
    const emojiButton = document.createElement('button');
    emojiButton.type = 'button';
    emojiButton.className = 'emoji-picker-button';
    emojiButton.innerHTML = emojiSVG;
    emojiButton.style.cssText = `
        position: absolute;
        right: 5px;
        top: 20%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        padding: 8px;
        border-radius: 6px;
        transition: background 0.2s, opacity 0.3s ease;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #666;
    `;
    
    emojiButton.onmouseenter = function() {
        this.style.background = '#f0f0f0';
    };
    emojiButton.onmouseleave = function() {
        this.style.background = 'none';
    };
    
    wrapper.appendChild(emojiButton);
    
    // Create picker instance
    const picker = new CustomEmojiPicker(textarea);
    textarea.customEmojiPicker = picker;
    
    // Function to toggle icon with smooth animation
    function toggleIcon(isOpen) {
        // Add fade animation
        emojiButton.style.opacity = '0';
        
        setTimeout(() => {
            emojiButton.innerHTML = isOpen ? closeSVG : emojiSVG;
            emojiButton.style.opacity = '1';
        }, 150); // Half of the transition duration for smooth effect
    }
    
    // Set the toggle callback for the picker
    picker.onToggleCallback = toggleIcon;
    
    // Button click handler
    emojiButton.onclick = (e) => {
        e.preventDefault();
        e.stopPropagation();
        picker.toggle();
    };
    
    return picker;
};

// Alias for backward compatibility
window.initEmojiPicker = window.initCustomEmojiPicker;

