INSERT INTO `Products`
  (`Id`, `Name`, `NameEng`, `ImageName`, `Type`,
   `Description`, `DescriptionEng`,
   `NutritionalValue`, `NutritionalValueEng`)
VALUES
  -- 1. Thyme Honey
  (
    1, 'Θυμαρίσιο Μέλι', 'Thyme Honey', 'thyme-honey.png', 1,
    'Παράγεται μόνο τους καλοκαιρινούς μήνες όταν ανθίζει το θυμάρι. Το χαρακτηριστικό του είναι η αρωματική γεύση και το χρυσαφένιο χρώμα. Στην Ελλάδα θεωρείται είδος πολυτελείας.',
    'Product only in the summer months when thyme blossoms. Its aromatic taste and golden color are its most prominent characteristics. In Greece, it is considered a luxury item.',
    'Υψηλή σε αντιοξειδωτικά και μέταλλα',
    'High in antioxidants and minerals'
  ),

  -- 2. Blossom Honey
  (
    2, 'Μέλι Ανθέων', 'Blossom Honey', 'blossom-honey.png', 1,
    'Το μέλι ανθέων παράγεται καθόλη τη διάρκεια του έτους. Κάθε ημέρα η γεύση του είναι μοναδική και απρόβλεπτη επειδή οι μέλισσες συλλέγουν γύρη από τα διαθέσιμα λουλούδια γύρω τους.',
    'Blossom honey is produced year-round. Every day its taste is unique and unpredictable because the bees collect pollen from the available flowers in their vicinity.',
    'Πλούσιο σε φυσικά σάκχαρα και ένζυμα',
    'Rich in natural sugars and enzymes'
  ),

  -- 3. Carob Honey
  (
    4, 'Χαρουπόμελο', 'Carob Honey', 'carob-honey.png', 1,
    'Κατανάλωση αυτού του μελιού φρέσκου δίνει μια πολύ έντονη γήινη γεύση που δεν αρέσει σε όλους. Αφήνοντάς το να ωριμάσει μετατρέπεται σε «καφέ χρυσό»!',
    'Eating this honey fresh gives off a very strong earthy flavor not for everyone. Leaving it to mature transforms it into brown gold!',
    'Υψηλό σε σίδηρο και μαγνήσιο',
    'High in iron and magnesium'
  ),

  -- 4. Sage Honey
  (
    3, 'Μέλι Φασκόμηλου', 'Sage Honey', 'sage-honey.png', 1,
    'Το μέλι φασκόμηλου έχει απαλή γεύση και ανοιχτό χρώμα. Είναι γνωστό για τις καταπραϋντικές ιδιότητές του και χρησιμοποιείται συχνά σε φυτικά σκευάσματα.',
    'Sage honey has a delicate flavor and light color. It is known for its soothing properties and is often used in herbal remedies.',
    'Περιέχει αντιφλεγμονώδεις ενώσεις',
    'Contains anti-inflammatory compounds'
  ),

  -- 5. Erica Honey
  (
    5, 'Μέλι Ερείκης', 'Erica Honey', 'erica-honey.png', 1,
    'Το μέλι ερείκης εκτιμάται για το σκούρο χρώμα του και την πλούσια, έντονη γεύση του. Συχνά αναζητείται για την υψηλή περιεκτικότητά του σε αντιοξειδωτικά.',
    'Erica honey is prized for its dark color and rich, robust flavor. It is often sought after for its high antioxidant content.',
    'Υψηλό σε αντιοξειδωτικά',
    'High in antioxidants'
  ),

  -- 6. Chewing Propolis
  (
    6, 'Μασώμενη Πρόπολη', 'Chewing Propolis', 'chewing-propolis.png', 2,
    'Η μασώμενη πρόπολη είναι γνωστή για τις φυσικές αντιβιοτικές και αντιφλεγμονώδεις ιδιότητές της. Υποστηρίζει την στοματική υγεία και ενισχύει το ανοσοποιητικό σύστημα.',
    'Chewing propolis is known for its natural antibiotic and anti-inflammatory properties. It supports oral health and boosts the immune system.',
    'Πλούσια σε βιοφλαβονοειδή',
    'Rich in bioflavonoids'
  ),

  -- 7. Royal Jelly
  (
    7, 'Βασιλικός Πολτός', 'Royal Jelly', 'royal-jelly.png', 2,
    'Ο βασιλικός πολτός είναι ένα θρεπτικό υποπροϊόν που παράγουν οι εργάτριες μέλισσες. Χρησιμοποιείται ως συμπλήρωμα διατροφής για την ενίσχυση της ζωτικότητας και της συνολικής υγείας.',
    'Royal jelly is a nutrient-rich substance produced by worker bees. It is used as a dietary supplement to promote vitality and overall health.',
    'Υψηλό σε βιταμίνες B1, B2, B6, B12',
    'High in vitamins B1, B2, B6, B12'
  ),

  -- 8. Edible Honeycomb
  (
    8, 'Βρώσιμη Κηρήθρα', 'Edible Honeycomb', 'edible-honeycomb.png', 2,
    'Η βρώσιμη κηρήθρα προσφέρει έναν μοναδικό και φυσικό τρόπο κατανάλωσης μελιού. Παρέχει μια γλυκιά, μασώμενη υφή και είναι ιδανική για τυρομεζέδες ή γλυκά.',
    'Edible honeycomb offers a unique and natural way to enjoy honey. It provides a sweet, chewy texture and is perfect for adding to cheese boards or desserts.',
    'Πλούσια σε βιταμίνες και ένζυμα',
    'Rich in vitamins and enzymes'
  ),

  -- 9. Fresh Pollen
  (
    9, 'Φρέσκια Γύρη', 'Fresh Pollen', 'fresh-pollen.png', 2,
    'Η φρέσκια γύρη είναι ένα πολύ θρεπτικό προϊόν μελισσών γεμάτο βιταμίνες, μέταλλα και πρωτεΐνες. Χρησιμοποιείται συχνά ως συμπλήρωμα διατροφής για ενέργεια και υποστήριξη του ανοσοποιητικού.',
    'Fresh pollen is a highly nutritious bee product packed with vitamins, minerals, and proteins. It is often used as a dietary supplement for energy and immune support.',
    'Υψηλή σε πρωτεΐνες, βιταμίνες και μέταλλα',
    'High in proteins, vitamins, and minerals'
  ),

  -- 10. Propolis Tincture
  (
    10, 'Βάμμα Πρόπολης', 'Propolis Tincture', 'propolis-tincture.png', 2,
    'Το εκχύλισμα πρόπολης σε υγρή μορφή είναι γνωστό για τις φαρμακευτικές του ιδιότητες. Παρέχει υποστήριξη στο ανοσοποιητικό, έχει αντιφλεγμονώδη και αντιβακτηριακή δράση.',
    'A liquid extract of propolis known for its medicinal properties. Provides immune support, and has anti-inflammatory and antibacterial properties.',
    'Πλούσια σε βιοφλαβονοειδή',
    'Rich in bioflavonoids'
  ),

  -- 11. Beeswax Candles
  (
    11, 'Κεριά από Μελισσοκέρι', 'Beeswax Candles', 'beeswax-candles.png', 4,
    'Φυσικά, χειροποίητα κεριά από μελισσοκέρι σε διάφορα σχήματα και μεγέθη. Διαρκούν πολύ, καίγονται καθαρά και έχουν φυσική μυρωδιά μελιού.',
    'Natural, handcrafted beeswax candles in various shapes and sizes. Long-lasting, clean-burning, and have a natural honey scent.',
    'Χωρίς τοξίνες, προάγει καθαρό αέρα',
    'Free from toxins, promotes clean air'
  ),

  -- 12. Beeswax Wraps
  (
    12, 'Κηρόπανα', 'Beeswax Wraps', 'beeswax-wraps.png', 4,
    'Φιλικές προς το περιβάλλον, επαναχρησιμοποιούμενες μεμβράνες φαγητού από μελισσοκέρι. Βιώσιμη εναλλακτική πλαστικής μεμβράνης που διατηρεί τα τρόφιμα φρέσκα.',
    'Eco-friendly, reusable food wraps made from beeswax. A sustainable alternative to plastic wrap that keeps food fresh.',
    'Αντιβακτηριακές, διαπνέουσες',
    'Antibacterial, breathable'
  ),

  -- 13. Honey Soap
  (
    13, 'Σαπούνι με Μέλι', 'Honey Soap', 'honey-soap.png', 3,
    'Χειροποίητο σαπούνι με μέλι και μελισσοκέρι. Ενυδατικό, αντιβακτηριακό και κατάλληλο για ευαίσθητο δέρμα.',
    'Handcrafted soap made with honey and beeswax. Moisturizing, antibacterial, and suitable for sensitive skin.',
    'Ενυδατικό, καταπραϋντικό',
    'Moisturizing, soothing'
  ),

  -- 14. Beeswax Lip Balm
  (
    14, 'Βάλσαμο Χειλιών με Μελισσοκέρι', 'Beeswax Lip Balm', 'beeswax-lip-balm.png', 3,
    'Φυσικό βάλσαμο χειλιών από μελισσοκέρι, μέλι και αιθέρια έλαια. Ενυδατικό, προστατευτικό και καταπραϋντικό.',
    'Natural lip balm made with beeswax, honey, and essential oils. Moisturizing, protective, and soothing for lips.',
    'Ενυδατικό, προστατευτικό',
    'Protective, hydrating'
  );
