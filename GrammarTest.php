<?php

require_once('grammar.php');

class EmberTest extends PHPUnit_Framework_TestCase
{

    public function testWordForm()
    {
        $N = GFactory::parseNP('ember');
        $this->assertEquals($N->vow, $N->makeNominativus()->vow);
    }

    public function testLexikon()
    {
        unset($N);
        $N = GFactory::parseNP('ember');
        $this->assertFalse($N->isVTMR(), 'ember is not VTMR');

        unset($N);
        $N = GFactory::parseNP('út');
        $this->assertTrue($N->isVTMR(), 'út is VTMR');

        unset($su);
        $su = GFactory::parseSuffixum('_Vk');
        $this->assertTrue($su->isVTMR(), 'plural suffixum is VTMR');

        $this->assertEquals('aktivista', (string) GFactory::parseNP('aktív')->appendSuffix(GFactory::parseSuffixum('ista')));
        $this->assertEquals('aktivizál', (string) GFactory::parseNP('aktív')->appendSuffix(GFactory::parseSuffixum('izál')));
        $this->assertEquals('aktivizmus', (string) GFactory::parseNP('aktív')->appendSuffix(GFactory::parseSuffixum('izmus')));
        $this->assertEquals('aktivitás', (string) GFactory::parseNP('aktív')->appendSuffix(GFactory::parseSuffixum('itás')));
        $this->assertEquals('miniatürizál', (string) GFactory::parseNP('miniatűr')->appendSuffix(GFactory::parseSuffixum('izál')));
        $this->assertEquals('urizál', (string) GFactory::parseNP('úr')->appendSuffix(GFactory::parseSuffixum('izál')));
        $this->assertEquals('fuzionál', (string) GFactory::parseNP('fúzió')->appendSuffix(GFactory::parseSuffixum('nál')));
        $this->assertEquals('szlavista', (string) GFactory::parseNP('szláv')->appendSuffix(GFactory::parseSuffixum('ista')));
        $this->assertEquals('privatizál', (string) GFactory::parseNP('privát')->appendSuffix(GFactory::parseSuffixum('izál')));

        $this->assertEquals('katonasága', (string) GFactory::parseNP('katona')->appendSuffix(GFactory::parseSuffixum('sÁg'))->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));
    }

    public function testPossessive()
    {
        unset($N);
        $N = GFactory::parseNP('barnulás');
        $this->assertEquals('barnulásom',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 1)));
        $this->assertEquals('barnulásod',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 2)));
        $this->assertEquals('barnulása',    (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));
        $this->assertEquals('barnulásunk',  (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(3, 1)));
        $this->assertEquals('barnulásotok', (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2)));
        $this->assertEquals('barnulásuk',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(3, 3)));

        unset($N);
        $N = GFactory::parseNP('pad');
        $this->assertEquals('padom',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 1)));
        $this->assertEquals('padod',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 2)));
        $this->assertEquals('padja',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));
        $this->assertEquals('padunk',  (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(3, 1)));
        $this->assertEquals('padotok', (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2)));
        $this->assertEquals('padjuk',  (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(3, 3)));

        $this->assertEquals('ladája',   (string) GFactory::parseNP('lada')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));
        $this->assertEquals('ládája',   (string) GFactory::parseNP('láda')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));
        $this->assertEquals('ládánk',   (string) GFactory::parseNP('láda')->appendSuffix(PossessiveSuffixum::makeNumPers(3, 1)));
        $this->assertEquals('bikája',   (string) GFactory::parseNP('bika')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));
        $this->assertEquals('bikánk',   (string) GFactory::parseNP('bika')->appendSuffix(PossessiveSuffixum::makeNumPers(3, 1)));

        $this->assertEquals('királya',   (string) GFactory::parseNP('király')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));
        $this->assertEquals('olaja',   (string) GFactory::parseNP('olaj')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));
        $this->assertEquals('báránya',   (string) GFactory::parseNP('bárány')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));
        $this->assertEquals('sasa',   (string) GFactory::parseNP('sas')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));
        $this->assertEquals('perece',   (string) GFactory::parseNP('perec')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));

        $this->assertEquals('nagyja',   (string) GFactory::parseNP('nagy')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));
        $this->assertEquals('sárkányja',   (string) GFactory::parseNP('sárkány')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));
        $this->assertEquals('kupecje',   (string) GFactory::parseNP('kupec')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));
        $this->assertEquals('kortesje',   (string) GFactory::parseNP('kortes')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));
        $this->assertEquals('maceszje',   (string) GFactory::parseNP('macesz')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));
        $this->assertEquals('trapézja',   (string) GFactory::parseNP('trapéz')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));
        $this->assertEquals('rasszja',   (string) GFactory::parseNP('rassz')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));

        $this->assertEquals('padja',   (string) GFactory::parseNP('pad')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));
        $this->assertEquals('embere',   (string) GFactory::parseNP('ember')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));

        $this->assertEquals('szenátora',   (string) GFactory::parseNP('szenátor')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));
        $this->assertEquals('minisztere',   (string) GFactory::parseNP('miniszter')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));
        $this->assertEquals('slágere',   (string) GFactory::parseNP('sláger')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));
        $this->assertEquals('jubileuma',   (string) GFactory::parseNP('jubileum')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));

        $this->assertTrue(GFactory::parseNP('kert')->isJaje());
        $this->assertEquals('galambja',   (string) GFactory::parseNP('galamb')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));
        $this->assertEquals('kertje',   (string) GFactory::parseNP('kert')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));
        $this->assertEquals('barackja',   (string) GFactory::parseNP('barack')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));
        $this->assertEquals('csontja',   (string) GFactory::parseNP('csont')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));
        $this->assertEquals('fánkja',   (string) GFactory::parseNP('fánk')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));
        $this->assertEquals('gondja',   (string) GFactory::parseNP('gond')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));
        $this->assertEquals('futballja',   (string) GFactory::parseNP('futball')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));
        $this->assertEquals('címzettje',   (string) GFactory::parseNP('címzett')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));
        $this->assertEquals('sakkja',   (string) GFactory::parseNP('sakk')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));
        $this->assertEquals('cseppje',   (string) GFactory::parseNP('csepp')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));

        unset($N);
        $N = GFactory::parseNP('vér');
        $this->assertEquals('vérem',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 1)));
        $this->assertEquals('véred',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 2)));
        $this->assertEquals('vére',    (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));
        $this->assertEquals('vérünk',  (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(3, 1)));
        $this->assertEquals('véretek', (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2)));
        $this->assertEquals('vérük',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(3, 3)));

        $this->assertEquals('véreim',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 1, 3)));
        $this->assertEquals('véreid',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 2, 3)));
        $this->assertEquals('vérei',    (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3, 3)));
        $this->assertEquals('véreink',  (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(3, 1, 3)));
        $this->assertEquals('véreitek', (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2, 3)));
        $this->assertEquals('véreik',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(3, 3, 3)));

        unset($N);
        $N = GFactory::parseNP('ős');
        $this->assertEquals('ősöm',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 1)));
        $this->assertEquals('ősöd',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 2)));
        $this->assertEquals('őse',    (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));
        $this->assertEquals('ősünk',  (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(3, 1)));
        $this->assertEquals('ősötök', (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2)));
        $this->assertEquals('ősük',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(3, 3)));

        $this->assertEquals('őseim',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 1, 3)));
        $this->assertEquals('őseid',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 2, 3)));
        $this->assertEquals('ősei',    (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3, 3)));
        $this->assertEquals('őseink',  (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(3, 1, 3)));
        $this->assertEquals('őseitek', (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2, 3)));
        $this->assertEquals('őseik',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(3, 3, 3)));

        unset($N);
        $N = GFactory::parseNP('ház');
        $this->assertEquals('házam',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 1, 1)));
        $this->assertEquals('házaim',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 1, 3)));
        $this->assertEquals('házaid',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 2, 3)));
        $this->assertEquals('házad',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 2, 1)));
        $this->assertEquals('háza',    (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3, 1)));
        $this->assertEquals('házai',    (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3, 3)));
        $this->assertEquals('házunk',  (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(3, 1, 1)));
        $this->assertEquals('házaink',  (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(3, 1, 3)));
        $this->assertEquals('házatok', (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2, 1)));
        $this->assertEquals('házaitok', (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2, 3)));
        $this->assertEquals('házuk',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(3, 3, 1)));
        $this->assertEquals('házaik',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(3, 3, 3)));

        unset($N);
        $N = GFactory::parseNP('marha');
        $this->assertEquals('marhám', (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 1, 1)));
        $this->assertEquals('marháim', (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 1, 3)));
        $this->assertEquals('marhád', (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 2, 1)));
        $this->assertEquals('marháid', (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 2, 3)));
        $this->assertEquals('marhája', (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3, 1)));
        $this->assertEquals('marhái', (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3, 3)));
        $this->assertEquals('marhánk', (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(3, 1, 1)));
        $this->assertEquals('marháink', (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(3, 1, 3)));
        $this->assertEquals('marhátok', (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2, 1)));
        $this->assertEquals('marháitok', (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2, 3)));
        $this->assertEquals('marhájuk', (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(3, 3, 1)));
        $this->assertEquals('marháik', (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(3, 3, 3)));
    }

    public function testPossessor()
    {
        unset($N);
        $N = GFactory::parseNP('ős');
        $this->assertEquals('őseimé',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 1, 3))->appendSuffix(PossessorSuffixum::makePossessor(1)));
        $this->assertEquals('őseidéi',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 2, 3))->appendSuffix(PossessorSuffixum::makePossessor(3)));
        $this->assertEquals('őseié',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3, 3))->appendSuffix(PossessorSuffixum::makePossessor(1)));
        $this->assertEquals('őseiéiről',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3, 3))->appendSuffix(PossessorSuffixum::makePossessor(3))->makeDelativus());

        unset($N);
        $N = GFactory::parseNP('ház');
        $this->assertEquals('házaimé',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 1, 3))->appendSuffix(PossessorSuffixum::makePossessor(1)));
        $this->assertEquals('házaiméi',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 1, 3))->appendSuffix(PossessorSuffixum::makePossessor(3)));
        $this->assertEquals('házaiméiról',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 1, 3))->appendSuffix(PossessorSuffixum::makePossessor(3))->makeDelativus());
        $this->assertEquals('házaié',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3, 3))->appendSuffix(PossessorSuffixum::makePossessor(1)));

        $this->assertEquals('Vargáé', (string) GFactory::parseNP('Varga')->appendSuffix(PossessorSuffixum::makePossessor()));
    }

    public function testOpening()
    {
        $this->assertTrue(GFactory::parseNP('ház')->isOpening());
        $this->assertTrue(GFactory::parseV('zöldül')->isOpening());
        $this->assertTrue(GFactory::parseV('zöldül')->appendSuffix(GFactory::parseSuffixum('Ás'))->isOpening());
        $this->assertTrue(GFactory::parseV('zöldül')->appendSuffix(GFactory::parseSuffixum('Ás'))->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2))->isOpening());
    }

    public function testAlternating()
    {
        $this->assertTrue(GFactory::parseNP('bátor')->isAlternating());
        $this->assertTrue(GFactory::parseSuffixum('_Vk')->isAlternating(), 'plural suffixum is volatile');
        $this->assertEquals('ajkak',  (string) GFactory::parseNP('ajak')->makePlural());
        $this->assertEquals('baglyok', (string) GFactory::parseNP('bagoly')->makePlural());
        $this->assertEquals('bajszok',  (string) GFactory::parseNP('bajusz')->makePlural());
        $this->assertEquals('bátrak',  (string) GFactory::parseNP('bátor')->makePlural());
        $this->assertEquals('dolgok',  (string) GFactory::parseNP('dolog')->makePlural());
        $this->assertEquals('hasznok', (string) GFactory::parseNP('haszon')->makePlural());
        $this->assertEquals('izmok',   (string) GFactory::parseNP('izom')->makePlural());
        $this->assertEquals('kazlak',  (string) GFactory::parseNP('kazal')->makePlural());
        $this->assertEquals('leplek',  (string) GFactory::parseNP('lepel')->makePlural());
        $this->assertEquals('majmok',  (string) GFactory::parseNP('majom')->makePlural());
        $this->assertEquals('piszkok', (string) GFactory::parseNP('piszok')->makePlural());
        $this->assertEquals('tornyok', (string) GFactory::parseNP('torony')->makePlural());
        $this->assertEquals('tücskök', (string) GFactory::parseNP('tücsök')->makePlural());
        $this->assertEquals('tükrök',  (string) GFactory::parseNP('tükör')->makePlural());
        $this->assertEquals('tülkök',  (string) GFactory::parseNP('tülök')->makePlural());
        $this->assertEquals('vackok',  (string) GFactory::parseNP('vacak')->makePlural());
        $this->assertEquals('álmok',   (string) GFactory::parseNP('álom')->makePlural());

        $this->assertEquals('lovak',   (string) GFactory::parseNP('ló')->makePlural());
        $this->assertEquals('havak',   (string) GFactory::parseNP('hó')->makePlural());
        $this->assertEquals('füvek',   (string) GFactory::parseNP('fű')->makePlural());

        $this->assertEquals('terhek',   (string) GFactory::parseNP('teher')->makePlural());
        $this->assertEquals('pelyhek',   (string) GFactory::parseNP('pehely')->makePlural());
        $this->assertEquals('kelyhek',   (string) GFactory::parseNP('kehely')->makePlural());

        // @todo verb szerző szerez
        // @todo verb törlő töröl
        
    }

    public function testPhonology()
    {
        $this->assertEquals('emberke', (string) GFactory::parseNP('ember')->appendSuffix(GFactory::parseSuffixum('kA')));
        $this->assertEquals('barnulásotoktól', (string) GFactory::parseV('barnul')->appendSuffix(GFactory::parseSuffixum('Ás'))->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2))->appendSuffix(GFactory::parseSuffixum('tÓl')));
        $this->assertEquals('zöldülésetek', (string) GFactory::parseV('zöldül')->appendSuffix(GFactory::parseSuffixum('Ás'))->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2)));
        $this->assertEquals('zöldülésetektől', (string) GFactory::parseV('zöldül')->appendSuffix(GFactory::parseSuffixum('Ás'))->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2))->appendSuffix(GFactory::parseSuffixum('tÓl')));
        $this->assertEquals('kékülés',  (string) GFactory::parseV('kékül')->appendSuffix(GFactory::parseSuffixum('Ás')));
        $this->assertEquals('kékülésetek',  (string) GFactory::parseNP('kék')->appendSuffix(GFactory::parseSuffixum('Ul'))->appendSuffix(GFactory::parseSuffixum('Ás'))->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2)));
        $this->assertEquals('kékülésetektől',  (string) GFactory::parseNP('kék')->appendSuffix(GFactory::parseSuffixum('Ul'))->appendSuffix(GFactory::parseSuffixum('Ás'))->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2))->appendSuffix(GFactory::parseSuffixum('tÓl')));
        $this->assertEquals('kéketeket', (string) GFactory::parseNP('kék')->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2))->appendSuffix(GFactory::parseSuffixum('_Vt')));
        $this->assertEquals('tökötöket', (string) GFactory::parseNP('tök')->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2))->appendSuffix(GFactory::parseSuffixum('_Vt')));
        $this->assertEquals('tökünkhöz', (string) GFactory::parseNP('tök')->appendSuffix(PossessiveSuffixum::makeNumPers(3, 1))->appendSuffix(GFactory::parseSuffixum('hOz')));
        $this->assertEquals('vizünkhöz', (string) GFactory::parseNP('víz')->appendSuffix(PossessiveSuffixum::makeNumPers(3, 1))->appendSuffix(GFactory::parseSuffixum('hOz')));
        $this->assertEquals('tüzeteket', (string) GFactory::parseNP('tűz')->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2))->appendSuffix(GFactory::parseSuffixum('_Vt')));
        $this->assertEquals('sorfőrötöket', (string) GFactory::parseNP('sorfőr')->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2))->appendSuffix(GFactory::parseSuffixum('_Vt')));
        $this->assertEquals('nyulatokat', (string) GFactory::parseNP('nyúl')->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2))->appendSuffix(GFactory::parseSuffixum('_Vt')));
        $this->assertEquals('házatokat', (string) GFactory::parseNP('ház')->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2))->appendSuffix(GFactory::parseSuffixum('_Vt')));
        $this->assertEquals('vizeteket', (string) GFactory::parseNP('víz')->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2))->appendSuffix(GFactory::parseSuffixum('_Vt')));
        $this->assertEquals('hidak', (string) GFactory::parseNP('híd')->appendSuffix(GFactory::parseSuffixum('_Vk')));
        $this->assertEquals('hidat', (string) GFactory::parseNP('híd')->appendSuffix(GFactory::parseSuffixum('_Vt')));
        $this->assertEquals('hidatokat', (string) GFactory::parseNP('híd')->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2))->appendSuffix(GFactory::parseSuffixum('_Vt')));
        $this->assertEquals('nyilakat', (string) GFactory::parseNP('nyíl')->appendSuffix(GFactory::parseSuffixum('_Vk'))->appendSuffix(GFactory::parseSuffixum('_Vt')));
        $this->assertEquals('nyilatokat', (string) GFactory::parseNP('nyíl')->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2))->appendSuffix(GFactory::parseSuffixum('_Vt')));
        $this->assertEquals('papírotokat', (string) GFactory::parseNP('papír')->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2))->appendSuffix(GFactory::parseSuffixum('_Vt')));
        $this->assertEquals('októbereteket', (string) GFactory::parseNP('október')->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2))->appendSuffix(GFactory::parseSuffixum('_Vt')));
        $this->assertEquals('tányérotokat', (string) GFactory::parseNP('tányér')->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2))->appendSuffix(GFactory::parseSuffixum('_Vt')));
        //$this->assertEquals('hotelotokat', (string) GFactory::parseNP('hotel')->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2))->appendSuffix(GFactory::parseSuffixum('_Vt')));
        $this->assertEquals('hoteleteket', (string) GFactory::parseNP('hotel')->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2))->appendSuffix(GFactory::parseSuffixum('_Vt')));
        $this->assertEquals('üvegeteket', (string) GFactory::parseNP('üveg')->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2))->appendSuffix(GFactory::parseSuffixum('_Vt')));
        $this->assertEquals('rövideteket', (string) GFactory::parseNP('rövid')->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2))->appendSuffix(GFactory::parseSuffixum('_Vt')));
        $this->assertEquals('szegényeteket', (string) GFactory::parseNP('szegény')->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2))->appendSuffix(GFactory::parseSuffixum('_Vt')));
        $this->assertEquals('matinétokat', (string) GFactory::parseNP('matiné')->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2))->appendSuffix(GFactory::parseSuffixum('_Vt')));
        //$this->assertEquals('matinéteket', (string) GFactory::parseNP('matiné')->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2))->appendSuffix(GFactory::parseSuffixum('_Vt')));
        $this->assertEquals('oxigének', (string) GFactory::parseNP('oxigén')->makePlural());
        $this->assertEquals('oxigént', (string) GFactory::parseNP('oxigén')->appendSuffix(GFactory::parseSuffixum('_Vt')));
        $this->assertEquals('oxigéneket', (string) GFactory::parseNP('oxigén')->makePlural()->appendSuffix(GFactory::parseSuffixum('_Vt')));
        $this->assertEquals('oxigéneteket', (string) GFactory::parseNP('oxigén')->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2))->appendSuffix(GFactory::parseSuffixum('_Vt')));
        //$this->assertEquals('agresszívotokat', (string) GFactory::parseNP('agresszív')->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2))->appendSuffix(GFactory::parseSuffixum('_Vt')));
        $this->assertEquals('agresszíveteket', (string) GFactory::parseNP('agresszív')->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2))->appendSuffix(GFactory::parseSuffixum('_Vt')));

        $this->assertEquals('házunk', (string) GFactory::parseNP('ház')->appendSuffix(PossessiveSuffixum::makeNumPers(3, 1)));
        $this->assertEquals('tüzünk', (string) GFactory::parseNP('tűz')->appendSuffix(PossessiveSuffixum::makeNumPers(3, 1)));
        $this->assertEquals('vizünk', (string) GFactory::parseNP('viz')->appendSuffix(PossessiveSuffixum::makeNumPers(3, 1)));

        $this->assertEquals('fűvel', (string) GFactory::parseNP('fű')->makeInstrumentalis());
        $this->assertEquals('fával', (string) GFactory::parseNP('fa')->makeInstrumentalis());
        $this->assertEquals('marhával', (string) GFactory::parseNP('marha')->makeInstrumentalis());
    }

    public function testAMNY()
    {
        $this->assertEquals('fát', (string) GFactory::parseNP('fa')->makeAccusativus());
        $this->assertEquals('almás', (string) GFactory::parseNP('alma')->appendSuffix(GFactory::parseSuffixum('_Vs')));
        //$this->assertEquals('tartják', (string) GFactory::parseV('tart')->
        $this->assertEquals('házában', (string) GFactory::parseNP('ház')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3))->makeInessivus());
        $this->assertEquals('létrám', (string) GFactory::parseNP('létra')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 1)));
        $this->assertEquals('marhái', (string) GFactory::parseNP('marha')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3, 3)));
        // @todo $this->assertEquals('kutyául', (string) GFactory::parseNP('kutya')->makeEssivusFormalis());
        $this->assertEquals('deltáig', (string) GFactory::parseNP('delta')->makeTerminativus());
        $this->assertEquals('Vargát', (string) GFactory::parseNP('Varga')->makeAccusativus());
        $this->assertEquals('portán', (string) GFactory::parseNP('porta')->makeSuperessivus());
        $this->assertEquals('lustát', (string) GFactory::parseNP('lusta')->makeAccusativus());
        $this->assertEquals('medvét', (string) GFactory::parseNP('medve')->makeAccusativus());
        $this->assertEquals('epét', (string) GFactory::parseNP('epe')->makeAccusativus());
        $this->assertEquals('képe', (string) GFactory::parseNP('kép')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3)));
        $this->assertEquals('képet', (string) GFactory::parseNP('kép')->makeAccusativus());
        $this->assertEquals('képét', (string) GFactory::parseNP('kép')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3))->makeAccusativus());
        $this->assertEquals('vesét', (string) GFactory::parseNP('vese')->makeAccusativus());
        $this->assertEquals('sörtét', (string) GFactory::parseNP('sörte')->makeAccusativus());
        $this->assertEquals('mércét', (string) GFactory::parseNP('mérce')->makeAccusativus());
        $this->assertEquals('csempét', (string) GFactory::parseNP('csempe')->makeAccusativus());
        $this->assertEquals('estét', (string) GFactory::parseNP('este')->makeAccusativus());
        $this->assertEquals('feketét', (string) GFactory::parseNP('fekete')->makeAccusativus());

        $this->assertTrue(GFactory::parseSuffixum('bAn')->isAMNYRight());
        $this->assertFalse(GFactory::parseSuffixum('kor')->isAMNYRight());
        $this->assertEquals('órakor', (string) GFactory::parseNP('óra')->makeTemporalis());
        $this->assertFalse(GFactory::parseSuffixum('ista')->isAMNYRight());
        $this->assertFalse(GFactory::parseSuffixum('izmus')->isAMNYRight());
        $this->assertFalse(GFactory::parseSuffixum('szOr')->isAMNYRight());
        $this->assertFalse(GFactory::parseSuffixum('sÁg')->isAMNYRight());
        $this->assertEquals('butaság', (string) GFactory::parseNP('buta')->appendSuffix(GFactory::parseSuffixum('sÁg')));
        $this->assertFalse(GFactory::parseSuffixum('i')->isAMNYRight());
        $this->assertEquals('Budai', (string) GFactory::parseNP('Buda')->appendSuffix(GFactory::parseSuffixum('i')));
        $this->assertEquals('Budái', (string) GFactory::parseNP('Buda')->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3, 3)));
        $this->assertFalse(GFactory::parseSuffixum('ként')->isAMNYRight());
        $this->assertEquals('butaként', (string) GFactory::parseNP('buta')->appendSuffix(GFactory::parseSuffixum('ként')));
        $this->assertEquals('butát', (string) GFactory::parseNP('buta')->makeAccusativus());

        $this->assertEquals('távoztát', (string) GFactory::parseNP('távozta')->makeAccusativus());
        $this->assertEquals('távoztakor', (string) GFactory::parseNP('távozta')->appendSuffix(GFactory::parseSuffixum('kor')));
        $this->assertEquals('katonák', (string) GFactory::parseNP('katona')->makePlural());
        $this->assertEquals('katonaság', (string) GFactory::parseNP('katona')->appendSuffix(GFactory::parseSuffixum('sÁg')));
        $this->assertEquals('hazait', (string) GFactory::parseNP('hazai')->makeAccusativus());
        //$this->assertEquals('hazaiak', (string) GFactory::parseNP('hazai')->makePlural());
        //$this->assertEquals('megyeiek', (string) GFactory::parseNP('megyei')->makePlural());
    }

    public function testVowelHarmony()
    {
        $this->assertEquals(true , Phonology::getPropagatedI('ember'), 'ember');
        $this->assertEquals(false, Phonology::getPropagatedI('ház'), 'ház');
        $this->assertEquals(true , Phonology::getPropagatedI('föld'), 'föld');
        $this->assertEquals(true , Phonology::getPropagatedI('kert'), 'kert');
        $this->assertEquals(true , Phonology::getPropagatedI('kéz'), 'kéz');
        $this->assertEquals(false, Phonology::getPropagatedI('út'), 'út');
        $this->assertEquals(true , Phonology::getPropagatedI('kövér'), 'kövér');
        $this->assertEquals(true , Phonology::getPropagatedI('sofőr'), 'sofőr');
        $this->assertEquals(false, Phonology::getPropagatedI('kőfal'), 'kőfal');
        $this->assertEquals(false, Phonology::getPropagatedI('bika'), 'bika');
        $this->assertEquals(false, Phonology::getPropagatedI('nüansz'), 'nüansz');

        $this->assertEquals(false, Phonology::getPropagatedU('ház'), 'ház');
        $this->assertEquals(false, Phonology::getPropagatedU('kert'), 'kert');
        $this->assertEquals(true , Phonology::getPropagatedU('föld'), 'föld');
        $this->assertEquals(false, Phonology::getPropagatedU('kéz'), 'kéz');

        $this->assertEquals(false, Phonology::needSuffixI('ház'), 'ház');
        $this->assertEquals(true , Phonology::needSuffixI('kert'), 'kert');
        $this->assertEquals(true , Phonology::needSuffixI('föld'), 'föld');
        $this->assertEquals(true , Phonology::needSuffixI('tök'), 'tök');
        $this->assertEquals(true , Phonology::needSuffixI('kéz'), 'kéz');

        $this->assertEquals(false, Phonology::needSuffixU('ház'), 'ház');
        $this->assertEquals(false, Phonology::needSuffixU('kert'), 'kert');
        $this->assertEquals(true , Phonology::needSuffixU('föld'), 'föld');
        $this->assertEquals(true , Phonology::needSuffixU('tök'), 'tök');
        $this->assertEquals(false, Phonology::needSuffixU('kéz'), 'kéz');

    }

    public function testNominalCases()
    {
        unset($N);
        $N = GFactory::parseNP('ember');
        $this->assertEquals('miatta', (string) $N->makeCausalis()->pronominalize());
        $this->assertEquals('ember', (string) $N->makeNominativus());
        $this->assertEquals('emberek', (string) $N->makePlural());
        $this->assertEquals('embert', (string) $N->makeAccusativus());
        $this->assertEquals('embereket', (string) $N->makePlural()->makeAccusativus());
        $this->assertEquals('emberért', (string) $N->makeCausalisFinalis());
        $this->assertEquals('emberekért', (string) $N->makePlural()->makeCausalisFinalis());
        $this->assertEquals('embernek', (string) $N->makeDativus());
        $this->assertEquals('embereknek', (string) $N->makePlural()->makeDativus());
        $this->assertEquals('embereknek', (string) $N->makePlural()->makeGenitivus());
        $this->assertEquals('emberrel', (string) $N->makeInstrumentalis());
        $this->assertEquals('emberekkel', (string) $N->makePlural()->makeInstrumentalis());
        $this->assertEquals('emberré', (string) $N->makeTranslativusFactivus());
        $this->assertEquals('emberként', (string) $N->makeFormativus());
        $this->assertEquals('emberül', (string) $N->makeEssivusFormalis());
        $this->assertEquals('emberbe', (string) $N->makeIllativus());
        $this->assertEquals('emberben', (string) $N->makeInessivus());
        $this->assertEquals('emberből', (string) $N->makeElativus());
        $this->assertEquals('emberre', (string) $N->makeSublativus());
        $this->assertEquals('emberen', (string) $N->makeSuperessivus());
        $this->assertEquals('emberről', (string) $N->makeDelativus());
        $this->assertEquals('emberhez', (string) $N->makeAllativus());
        $this->assertEquals('embernél', (string) $N->makeAdessivus());
        $this->assertEquals('embertől', (string) $N->makeAblativus());
        $this->assertEquals('emberig', (string) $N->makeTerminativus());

        unset($N);
        $N = GFactory::parseNP('út');
        $this->assertEquals('út', (string) $N->makeNominativus());
        $this->assertEquals('utak', (string) $N->makePlural());
        $this->assertEquals('utat', (string) $N->makeAccusativus());
        $this->assertEquals('útért', (string) $N->makeCausalisFinalis());
        $this->assertEquals('útnak', (string) $N->makeDativus());
        $this->assertEquals('úttal', (string) $N->makeInstrumentalis());
        $this->assertEquals('úttá', (string) $N->makeTranslativusFactivus());
        $this->assertEquals('útként', (string) $N->makeFormativus());
        $this->assertFalse(Phonology::needSuffixI('út'));
        $this->assertEquals('U-,O', $N->needSuffixPhonocode());
        $this->assertEquals('útul', (string) $N->makeEssivusFormalis());
        $this->assertEquals('útba', (string) $N->makeIllativus());
        $this->assertEquals('útban', (string) $N->makeInessivus());
        $this->assertEquals('útból', (string) $N->makeElativus());
        $this->assertEquals('útra', (string) $N->makeSublativus());
        $this->assertEquals('úton', (string) $N->makeSuperessivus());
        $this->assertEquals('útról', (string) $N->makeDelativus());
        $this->assertEquals('úthoz', (string) $N->makeAllativus());
        $this->assertEquals('útnál', (string) $N->makeAdessivus());
        $this->assertEquals('úttól', (string) $N->makeAblativus());
        $this->assertEquals('útig', (string) $N->makeTerminativus());

        unset($N);
        $N = GFactory::parseNP('nyár');
        $this->assertEquals('nyár', (string) $N->makeNominativus());
        $this->assertEquals('nyarak', (string) $N->makePlural());
        $this->assertEquals('nyarat', (string) $N->makeAccusativus());
        $this->assertEquals('nyárhoz', (string) $N->makeAllativus());

        unset($N);
        $N = GFactory::parseNP('ház');
        $this->assertEquals('ház', (string) $N->makeNominativus());
        $this->assertEquals('házak', (string) $N->makePlural());
        $this->assertEquals('házat', (string) $N->makeAccusativus());
        $this->assertEquals('házhoz', (string) $N->makeAllativus());

        unset($N);
        $N = GFactory::parseNP('gáz');
        $this->assertEquals('gáz', (string) $N->makeNominativus());
        $this->assertEquals('gázok', (string) $N->makePlural());
        $this->assertEquals('gázt', (string) $N->makeAccusativus());

        unset($N);
        $N = GFactory::parseNP('tök');
        $this->assertEquals('tök', (string) $N->makeNominativus());
        $this->assertEquals('tökök', (string) $N->makePlural());
        $this->assertEquals('tököt', (string) $N->makeAccusativus());
        $this->assertEquals('tökhöz', (string) $N->makeAllativus());

        unset($N);
        $N = GFactory::parseNP('föld');
        $this->assertEquals('föld', (string) $N->makeNominativus());
        $this->assertEquals('földek', (string) $N->makePlural());
        $this->assertEquals('földet', (string) $N->makeAccusativus());
        $this->assertEquals('földért', (string) $N->makeCausalisFinalis());
        $this->assertEquals('földnek', (string) $N->makeDativus());
        $this->assertEquals('földdel', (string) $N->makeInstrumentalis());
        $this->assertEquals('földdé', (string) $N->makeTranslativusFactivus());
        $this->assertEquals('földön', (string) $N->makeSuperessivus());
        $this->assertEquals('földhöz', (string) $N->makeAllativus());
        $this->assertEquals('földtől', (string) $N->makeAblativus());

    }

    public function testVirtualNominalCases()
    {
        unset($N);
        $N = GFactory::parseNP('ember');
        $this->assertEquals('embernek', (string) $N->makeGenitivus());
        $this->assertEquals('ember miatt', (string) $N->makeCausalis());
        $this->assertEquals('emberen keresztül', (string) $N->makePerlativus());
        $this->assertEquals('emberen át', (string) $N->makeProlativus());
        $this->assertEquals('emberen át', (string) $N->makeVialis());
        $this->assertEquals('ember alatt', (string) $N->makeSubessivus());
        $this->assertEquals('ember mentén', (string) $N->makeProsecutivus());

        unset($N);
        $N = GFactory::parseNP('föld');
        $this->assertEquals('földön keresztül', (string) $N->makePerlativus());

        unset($N);
        $N = GFactory::parseNP('út');
        $this->assertEquals('úton keresztül', (string) $N->makePerlativus());
    }

        /* @see esetek.html
        miattam
        miattad
        miatta
        miattunk
        miattatok
        miattuk

        nélkül
        szerint
        ellenére
        végett
        ellen
        miatt
        révén
        fölé
        felett
        fölött
        fölül
        mellett
        alá
        alatt
        alól
        mellé
        mellett
        mellől
        felől
        elé
        előtt
        elől
        felé
        iránt
        felől
        mögé
        mögött
        megett
        mögül
        túlra
        utánra
        túl
        után
        túlról
        köré
        körül
        körött

        előtt
        fogva HRNU
        alatt
        után

        gyanánt
        helyett

        képest HRNU
        nézve HRNU

    public function testNU()
    {
        $this->markTestSkipped();

    }
         */

    public function testiVerbal()
    {
        unset($V);
        $V = & GFactory::parseV('olvas');
        $this->assertTrue($V->matchCase('13100'));
        $this->assertTrue($V->conjugate(1, 1, 1, 0, 0)->matchCase('11100'));
        $this->assertTrue($V->conjugate(1, 2, 3, 0, 3)->matchCase('.23.[23]'));
        $this->assertTrue($V->conjugate(3, 2, 1, 0, 0)->matchCase('13100|32100'));
        $this->assertTrue($V->conjugate(1, 1, 1, -1, 0)->matchCase('..[23]..|...9.'));
    }

    public function testVerbConjugation()
    {
        unset($V);
        $V = & GFactory::parseV('olvas');
        $this->assertEquals('olvas', (string) $V->getCitationForm());
        $this->assertEquals('olvastál volna', (string) $V->conjugate(1, 2, 2, -1, 0));
        $this->assertEquals('olvastad volna', (string) $V->conjugate(1, 2, 2, -1, 3));

        $this->checkConjugation(GFactory::parseV('olvas'), array(
            'olvasok', 'olvasol', 'olvas', 'olvasunk', 'olvastok', 'olvasnak', 
            'olvasom', 'olvasod', 'olvassa', 'olvassuk', 'olvassátok', 'olvassák', 
            'olvastam', 'olvastál', 'olvasott', 'olvastunk', 'olvastatok', 'olvastak', 
            'olvastam', 'olvastad', 'olvasta', 'olvastuk', 'olvastátok', 'olvasták', 
            'olvasnék', 'olvasnál', 'olvasna', 'olvasnánk', 'olvasnátok', 'olvasnának', 
            'olvasnám', 'olvasnád', 'olvasná', 'olvasnánk', 'olvasnátok', 'olvasnák', 
            'olvassak', 'olvassál', 'olvasson', 'olvassunk', 'olvassatok', 'olvassanak',  // @todo olvass
            'olvassam', 'olvassad', 'olvassa', 'olvassuk', 'olvassátok', 'olvassák', // @todo olvasd
        ));

        $this->assertTrue(GFactory::parseV('tesz')->isSZV);
        $this->assertEquals('teszlek', (string) GFactory::parseV('tesz')->conjugate(1, 1, 1, 0, 2));
        $this->assertEquals('tettelek', (string) GFactory::parseV('tesz')->conjugate(1, 1, 1, -1, 2));
        $this->assertEquals('tennélek', (string) GFactory::parseV('tesz')->conjugate(1, 1, 2, 0, 2));
        $this->assertEquals('tegyelek', (string) GFactory::parseV('tesz')->conjugate(1, 1, 3, 0, 2));


        $this->checkConjugation(GFactory::parseV('tesz'), array(
            'teszek', 'teszel', 'tesz', 'teszünk', 'tesztek', 'tesznek', 
            'teszem', 'teszed', 'teszi', 'tesszük', 'teszitek', 'teszik', 
            'tettem', 'tettél', 'tett', 'tettünk', 'tettetek', 'tettek', 
            'tettem', 'tetted', 'tette', 'tettük', 'tettétek', 'tették', 
            'tennék', 'tennél', 'tenne', 'tennénk', 'tennétek', 'tennének', 
            'tenném', 'tennéd', 'tenné', 'tennénk', 'tennétek', 'tennék', 
            'tegyek', 'tegyél', 'tegyen', 'tegyünk', 'tegyetek', 'tegyenek', 
            'tegyem', 'tegyed', 'tegye', 'tegyük', 'tegyétek', 'tegyék', 
        ));

        $this->checkConjugation(GFactory::parseV('hisz'), array(
            'hiszek', 'hiszel', 'hisz', 'hiszünk', 'hisztek', 'hisznek', 
            'hiszem', 'hiszed', 'hiszi', 'hisszük', 'hiszitek', 'hiszik', 
            'hittem', 'hittél', 'hitt', 'hittünk', 'hittetek', 'hittek', 
            'hittem', 'hitted', 'hitte', 'hittük', 'hittétek', 'hitték', 
            'hinnék', 'hinnél', 'hinne', 'hinnénk', 'hinnétek', 'hinnének', 
            'hinném', 'hinnéd', 'hinné', 'hinnénk', 'hinnétek', 'hinnék', 
            'higgyek', 'higgyél', 'higgyen', 'higgyünk', 'higgyetek', 'higgyenek', 
            'higgyem', 'higgyed', 'higgye', 'higgyük', 'higgyétek', 'higgyék', 
        ));

        $this->checkConjugation(GFactory::parseV('esz'), array(
            'eszek', 'eszel', 'eszik', 'eszünk', 'esztek', 'esznek', // @todo eszem
            'eszem', 'eszed', 'eszi', 'esszük', 'eszitek', 'eszik', 
            'ettem', 'ettél', 'evett', 'ettünk', 'ettetek', 'ettek', 
            'ettem', 'etted', 'ette', 'ettük', 'ettétek', 'ették', 
            'ennék', 'ennél', 'enne', 'ennénk', 'ennétek', 'ennének',
            'enném', 'ennéd', 'enné', 'ennénk', 'ennétek', 'ennék', 
            'egyek', 'egyél', 'egyen', 'együnk', 'egyetek', 'egyenek', 
            'egyem', 'egyed', 'egye', 'együk', 'egyétek', 'egyék', 
        ));

        $this->checkConjugation(GFactory::parseV('isz'), array(
            'iszok', 'iszol', 'iszik', 'iszunk', 'isztok', 'isznak', // @todo iszom
            'iszom', 'iszod', 'issza', 'isszuk', 'isszátok', 'isszák', 
            'ittam', 'ittál', 'ivott', 'ittunk', 'ittatok', 'ittak', 
            'ittam', 'ittad', 'itta', 'ittuk', 'ittátok', 'itták', 
            'innék', 'innál', 'inna', 'innánk', 'innátok', 'innának', // @todo innák
            'innám', 'innád', 'inná', 'innánk', 'innátok', 'innák', 
            'igyak', 'igyál', 'igyon', 'igyunk', 'igyatok', 'igyanak', 
            'igyam', 'igyad', 'igya', 'igyuk', 'igyátok', 'igyák', 
        ));

        $this->assertEquals('űzlek', (string) GFactory::parseV('űz')->conjugate(1, 1, 1, 0, 2));
        $this->assertEquals('űztelek', (string) GFactory::parseV('űz')->conjugate(1, 1, 1, -1, 2));
        $this->assertEquals('űznélek', (string) GFactory::parseV('űz')->conjugate(1, 1, 2, 0, 2));
        $this->assertEquals('űzzelek', (string) GFactory::parseV('űz')->conjugate(1, 1, 3, 0, 2));

        $this->checkConjugation(GFactory::parseV('űz'), array(
            'űzök', 'űzöl', 'űz', 'űzünk', 'űztök', 'űznek', 
            'űzöm', 'űzöd', 'űzi', 'űzzük', 'űzitek', 'űzik', 
            'űztem', 'űztél', 'űzött', 'űztünk', 'űztetek', 'űztek', 
            'űztem', 'űzted', 'űzte', 'űztük', 'űztétek', 'űzték', 
            'űznék', 'űznél', 'űzne', 'űznénk', 'űznétek', 'űznének', 
            'űzném', 'űznéd', 'űzné', 'űznénk', 'űznétek', 'űznék', 
            'űzzek', 'űzzél', 'űzzön', 'űzzünk', 'űzzetek', 'űzzenek', 
            'űzzem', 'űzzed', 'űzze', 'űzzük', 'űzzétek', 'űzzék',
        ));

        $this->assertTrue(GFactory::parseV('költ')->needSuffixI());
        $this->assertTrue(GFactory::parseV('költ')->needSuffixU());

        $this->checkConjugation(GFactory::parseV('költ'), array(
            'költök', 'költesz', 'költ', 'költünk', 'költötök', 'költenek', 
            'költöm', 'költöd', 'költi', 'költjük', 'költitek', 'költik', 
            'költöttem', 'költöttél', 'költött', 'költöttünk', 'költöttetek', 'költöttek', 
            'költöttem', 'költötted', 'költötte', 'költöttük', 'költöttétek', 'költötték', 
            'költenék', 'költenél', 'költene', 'költenénk', 'költenétek', 'költenének', 
            'költeném', 'költenéd', 'költené', 'költenénk', 'költenétek', 'költenék', 
            'költsek', 'költsél', 'költsön', 'költsünk', 'költsetek', 'költsenek', // költs
            'költsem', 'költsed', 'költse', 'költsük', 'költsétek', 'költsék', // költsd
        ));

        $this->assertTrue(GFactory::parseV('lő')->isPlusV);

        $this->assertEquals('lőlek', (string) GFactory::parseV('lő')->conjugate(1, 1, 1, 0, 2));
        $this->assertEquals('lőttelek', (string) GFactory::parseV('lő')->conjugate(1, 1, 1, -1, 2));
        $this->assertEquals('lőnélek', (string) GFactory::parseV('lő')->conjugate(1, 1, 2, 0, 2));
        $this->assertEquals('lőjelek', (string) GFactory::parseV('lő')->conjugate(1, 1, 3, 0, 2));

        $this->checkConjugation(GFactory::parseV('lő'), array(
            'lövök', 'lősz', 'lő', 'lövünk', 'lőtök', 'lőnek',
            'lövöm', 'lövöd', 'lövi', 'lőjük', 'lövitek', 'lövik',
            'lőttem', 'lőttél', 'lőtt', 'lőttünk', 'lőttetek', 'lőttek',
            'lőttem', 'lőtted', 'lőtte', 'lőttük', 'lőttétek', 'lőtték',
            'lőnék', 'lőnél', 'lőne', 'lőnénk', 'lőnétek', 'lőnének',
            'lőném', 'lőnéd', 'lőné', 'lőnénk', 'lőnétek', 'lőnék',
            'lőjek', 'lőjél', 'lőjön', 'lőjünk', 'lőjetek', 'lőjenek', // @todo lőj
            'lőjem', 'lőjed', 'lője', 'lőjük', 'lőjétek', 'lőjék', // @todo lődd
        ));

        $this->checkConjugation(GFactory::parseV('ró'), array(
            'rovok', 'rósz', 'ró', 'rovunk', 'rótok', 'rónak',
            'rovom', 'rovod', 'rója', 'rójuk', 'rójátok', 'róják',
            'róttam', 'róttál', 'rótt', 'róttunk', 'róttatok', 'róttak',
            'róttam', 'róttad', 'rótta', 'róttuk', 'róttátok', 'rótták',
            'rónék', 'rónál', 'róna', 'rónánk', 'rónátok', 'rónának',
            'rónám', 'rónád', 'róná', 'rónánk', 'rónátok', 'rónák',
            'rójak', 'rójál', 'rójon', 'rójunk', 'rójatok', 'rójanak',
            'rójam', 'rójad', 'rója', 'rójuk', 'rójátok', 'róják', // @todo ródd
        ));

        $this->checkConjugation(GFactory::parseV('alsz'), array(
            'alszok', 'alszol', 'alszik', 'alszunk', 'alszotok', 'alszanak',
            'alszom', 'alszod', 'alussza', 'alusszuk', 'alusszátok', 'alusszák',
            'aludtam', 'aludtál', 'aludt', 'aludtunk', 'aludtatok', 'aludtak',
            'aludtam', 'aludtad', 'aludta', 'aludtuk', 'aludtátok', 'aludták',
            'aludnék', 'aludnál', 'aludna', 'aludnánk', 'aludnátok', 'aludnának',
            'aludnám', 'aludnád', 'aludná', 'aludnánk', 'aludnátok', 'aludnák',
            'aludjak', 'aludjál', 'aludjon', 'aludjunk', 'aludjatok', 'aludjanak',
            'aludjam', 'aludjad', 'aludja', 'aludjuk', 'aludjátok', 'aludják',
        ));
        /*
        @todo vár ül ért bont költ
        eszik

        //sz/v, full list
        lesz
        tesz tesz tev te
        vesz
        hisz
        visz
        eszik
        iszik

        // @todo more on p. 219.

        fogok
        fogsz
        fog
        fogunk
        fogtok
        fognak

        fogom
        fogod
        fogja
        fogjuk
        fogjátok
        fogják

        // VerbAux extends HeadedExpression

        olvasni fogok
        olvasni fogsz
        olvasni fog
        olvasni fogunk
        olvasni fogtok
        olvasni fognak

        olvasni fogom
        olvasni fogod
        olvasni fogja
        olvasni fogjuk
        olvasni fogjátok
        olvasni fogják

         */
    }

    public function testDoubleConsonant()
    {
        $this->assertEquals('bb', Phonology::doubleConsonant('b'));
        $this->assertEquals('bb', Phonology::doubleConsonant('bb'));
        $this->assertEquals('cc', Phonology::doubleConsonant('c'));
        $this->assertEquals('ccs', Phonology::doubleConsonant('cs'));
        $this->assertEquals('ccs', Phonology::doubleConsonant('ccs'));
        $this->assertEquals('dd', Phonology::doubleConsonant('d'));
        $this->assertEquals('ddz', Phonology::doubleConsonant('dz'));
        $this->assertEquals('ddz', Phonology::doubleConsonant('ddz'));
        $this->assertEquals('ddzs', Phonology::doubleConsonant('dzs'));
        $this->assertEquals('ddzs', Phonology::doubleConsonant('ddzs'));
        $this->assertEquals('ff', Phonology::doubleConsonant('f'));
        $this->assertEquals('ff', Phonology::doubleConsonant('ff'));
        $this->assertEquals('gg', Phonology::doubleConsonant('g'));
        $this->assertEquals('gg', Phonology::doubleConsonant('gg'));
        $this->assertEquals('ggy', Phonology::doubleConsonant('gy'));
        $this->assertEquals('ggy', Phonology::doubleConsonant('ggy'));
        $this->assertEquals('hh', Phonology::doubleConsonant('h'));
        $this->assertEquals('jj', Phonology::doubleConsonant('j'));
        $this->assertEquals('jj', Phonology::doubleConsonant('jj'));
        $this->assertEquals('kk', Phonology::doubleConsonant('k'));
        $this->assertEquals('kk', Phonology::doubleConsonant('kk'));
        $this->assertEquals('ll', Phonology::doubleConsonant('l'));
        $this->assertEquals('ll', Phonology::doubleConsonant('ll'));
        $this->assertEquals('lly', Phonology::doubleConsonant('ly'));
        $this->assertEquals('lly', Phonology::doubleConsonant('lly'));
        $this->assertEquals('mm', Phonology::doubleConsonant('m'));
        $this->assertEquals('mm', Phonology::doubleConsonant('mm'));
        $this->assertEquals('nn', Phonology::doubleConsonant('n'));
        $this->assertEquals('nn', Phonology::doubleConsonant('nn'));
        $this->assertEquals('nny', Phonology::doubleConsonant('ny'));
        $this->assertEquals('nny', Phonology::doubleConsonant('nny'));
        $this->assertEquals('pp', Phonology::doubleConsonant('p'));
        $this->assertEquals('pp', Phonology::doubleConsonant('pp'));
        $this->assertEquals('qq', Phonology::doubleConsonant('q'));
        $this->assertEquals('rr', Phonology::doubleConsonant('r'));
        $this->assertEquals('rr', Phonology::doubleConsonant('rr'));
        $this->assertEquals('ss', Phonology::doubleConsonant('s'));
        $this->assertEquals('ss', Phonology::doubleConsonant('ss'));
        $this->assertEquals('ssz', Phonology::doubleConsonant('sz'));
        $this->assertEquals('ssz', Phonology::doubleConsonant('ssz'));
        $this->assertEquals('tt', Phonology::doubleConsonant('t'));
        $this->assertEquals('tt', Phonology::doubleConsonant('tt'));
        $this->assertEquals('tty', Phonology::doubleConsonant('ty'));
        $this->assertEquals('tty', Phonology::doubleConsonant('tty'));
        $this->assertEquals('vv', Phonology::doubleConsonant('v'));
        $this->assertEquals('vv', Phonology::doubleConsonant('vv'));
        $this->assertEquals('ww', Phonology::doubleConsonant('w'));
        $this->assertEquals('xx', Phonology::doubleConsonant('x'));
        $this->assertEquals('zz', Phonology::doubleConsonant('z'));
        $this->assertEquals('zz', Phonology::doubleConsonant('zz'));
        $this->assertEquals('zzs', Phonology::doubleConsonant('zs'));
        $this->assertEquals('zzs', Phonology::doubleConsonant('zzs'));
    }

    public function testLastConsonant()
    {
        $this->assertEquals('b', Phonology::getLastConsonant('galamb'));
        $this->assertEquals('bb', Phonology::getLastConsonant('szebb'));
        $this->assertEquals('c', Phonology::getLastConsonant('léc'));
        $this->assertEquals('cs', Phonology::getLastConsonant('mécs'));
        $this->assertEquals('ccs', Phonology::getLastConsonant('meccs'));
        $this->assertEquals('d', Phonology::getLastConsonant('fajd'));
        $this->assertEquals('dz', Phonology::getLastConsonant('edz'));
        $this->assertEquals('ddz', Phonology::getLastConsonant('xeddz'));
        $this->assertEquals('dzs', Phonology::getLastConsonant('bridzs'));
        $this->assertEquals('ddzs', Phonology::getLastConsonant('briddzs'));
        $this->assertEquals('f', Phonology::getLastConsonant('xef'));
        $this->assertEquals('ff', Phonology::getLastConsonant('xeff'));
        $this->assertEquals('g', Phonology::getLastConsonant('ág'));
        $this->assertEquals('gg', Phonology::getLastConsonant('agg'));
        $this->assertEquals('gy', Phonology::getLastConsonant('megy'));
        $this->assertEquals('ggy', Phonology::getLastConsonant('meggy'));
        $this->assertEquals('h', Phonology::getLastConsonant('düh'));
        $this->assertEquals('j', Phonology::getLastConsonant('díj'));
        $this->assertEquals('jj', Phonology::getLastConsonant('fejj'));
        $this->assertEquals('k', Phonology::getLastConsonant('mák'));
        $this->assertEquals('kk', Phonology::getLastConsonant('makk'));
        $this->assertEquals('l', Phonology::getLastConsonant('ál'));
        $this->assertEquals('ll', Phonology::getLastConsonant('áll'));
        $this->assertEquals('ly', Phonology::getLastConsonant('mély'));
        $this->assertEquals('lly', Phonology::getLastConsonant('gally'));
        $this->assertEquals('m', Phonology::getLastConsonant('ham'));
        $this->assertEquals('mm', Phonology::getLastConsonant('hamm'));
        $this->assertEquals('n', Phonology::getLastConsonant('mén'));
        $this->assertEquals('nn', Phonology::getLastConsonant('benn'));
        $this->assertEquals('ny', Phonology::getLastConsonant('lány'));
        $this->assertEquals('nny', Phonology::getLastConsonant('szenny'));
        $this->assertEquals('p', Phonology::getLastConsonant('szép'));
        $this->assertEquals('pp', Phonology::getLastConsonant('csepp'));
        $this->assertEquals('q', Phonology::getLastConsonant('faq'));
        $this->assertEquals('r', Phonology::getLastConsonant('dir'));
        $this->assertEquals('rr', Phonology::getLastConsonant('durr'));
        $this->assertEquals('s', Phonology::getLastConsonant('dés'));
        $this->assertEquals('ss', Phonology::getLastConsonant('ess'));
        $this->assertEquals('sz', Phonology::getLastConsonant('ész'));
        $this->assertEquals('ssz', Phonology::getLastConsonant('xessz'));
        $this->assertEquals('t', Phonology::getLastConsonant('lát'));
        $this->assertEquals('tt', Phonology::getLastConsonant('lőtt'));
        $this->assertEquals('ty', Phonology::getLastConsonant('báty'));
        $this->assertEquals('tty', Phonology::getLastConsonant('xetty'));
        $this->assertEquals('v', Phonology::getLastConsonant('hamv'));
        $this->assertEquals('vv', Phonology::getLastConsonant('xevv'));
        $this->assertEquals('w', Phonology::getLastConsonant('how'));
        $this->assertEquals('x', Phonology::getLastConsonant('bix'));
        $this->assertEquals('z', Phonology::getLastConsonant('bűz'));
        $this->assertEquals('zz', Phonology::getLastConsonant('bízz'));
        $this->assertEquals('zs', Phonology::getLastConsonant('bézs'));
        $this->assertEquals('zzs', Phonology::getLastConsonant('xezzs'));
    }

    public function checkConjugation(& $V, $verbforms)
    {
        $conjugations = array(
            array(1, 1, 1, 0, 0),
            array(1, 2, 1, 0, 0),
            array(1, 3, 1, 0, 0),
            array(3, 1, 1, 0, 0),
            array(3, 2, 1, 0, 0),
            array(3, 3, 1, 0, 0),

            array(1, 1, 1, 0, 3),
            array(1, 2, 1, 0, 3),
            array(1, 3, 1, 0, 3),
            array(3, 1, 1, 0, 3),
            array(3, 2, 1, 0, 3),
            array(3, 3, 1, 0, 3),

            array(1, 1, 1, -1, 0),
            array(1, 2, 1, -1, 0),
            array(1, 3, 1, -1, 0),
            array(3, 1, 1, -1, 0),
            array(3, 2, 1, -1, 0),
            array(3, 3, 1, -1, 0),

            array(1, 1, 1, -1, 3),
            array(1, 2, 1, -1, 3),
            array(1, 3, 1, -1, 3),
            array(3, 1, 1, -1, 3),
            array(3, 2, 1, -1, 3),
            array(3, 3, 1, -1, 3),

            array(1, 1, 2, 0, 0),
            array(1, 2, 2, 0, 0),
            array(1, 3, 2, 0, 0),
            array(3, 1, 2, 0, 0),
            array(3, 2, 2, 0, 0),
            array(3, 3, 2, 0, 0),

            array(1, 1, 2, 0, 3),
            array(1, 2, 2, 0, 3),
            array(1, 3, 2, 0, 3),
            array(3, 1, 2, 0, 3),
            array(3, 2, 2, 0, 3),
            array(3, 3, 2, 0, 3),

            array(1, 1, 3, 0, 0),
            array(1, 2, 3, 0, 0),
            array(1, 3, 3, 0, 0),
            array(3, 1, 3, 0, 0),
            array(3, 2, 3, 0, 0),
            array(3, 3, 3, 0, 0),

            array(1, 1, 3, 0, 3),
            array(1, 2, 3, 0, 3),
            array(1, 3, 3, 0, 3),
            array(3, 1, 3, 0, 3),
            array(3, 2, 3, 0, 3),
            array(3, 3, 3, 0, 3),
        );

        foreach ($conjugations as $i => $conjugation)
        {
            if (empty($verbforms[$i]))
                continue;
            $expected = $verbforms[$i];
            $actual = (string) call_user_func_array(array(& $V, 'conjugate'), $conjugation);
            $this->assertEquals($expected, $actual, '('.implode(',', $conjugation).") of '$V' should be '$expected', not '$actual'.");
        }
    }

}

?>
