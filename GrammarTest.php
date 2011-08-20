<?php

require_once('grammar.php');

class EmberTest extends PHPUnit_Framework_TestCase
{

    /*
    public function runAll()
    {
        $this->testAll();
    }

    public function assertEquals($expected, $actual)
    {
        assert('$expected === $actual');
        print "$actual\n";
    }
     */

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
        $this->assertEquals('házaim',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 1, 3)));
        $this->assertEquals('házaid',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 2, 3)));
        $this->assertEquals('házai',    (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3, 3)));
        $this->assertEquals('házaink',  (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(3, 1, 3)));
        $this->assertEquals('házaitok', (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2, 3)));
        $this->assertEquals('házaik',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(3, 3, 3)));
    }

    public function testPossessor()
    {
        unset($N);
        $N = GFactory::parseNP('ős');
        $this->assertEquals('őseimé',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 1, 3))->appendSuffix(PossessiveSuffixum::makePossessor(1)));
        $this->assertEquals('őseidéi',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 2, 3))->appendSuffix(PossessiveSuffixum::makePossessor(3)));
        $this->assertEquals('őseiéiről',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 3, 3))->appendSuffix(PossessiveSuffixum::makePossessor(3))->makeDelativus());

        unset($N);
        $N = GFactory::parseNP('ház');
        $this->assertEquals('házaimé',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 1, 3))->appendSuffix(PossessiveSuffixum::makePossessor(1)));
        $this->assertEquals('házaiméi',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 1, 3))->appendSuffix(PossessiveSuffixum::makePossessor(3)));
        $this->assertEquals('házaiméiról',   (string) $N->appendSuffix(PossessiveSuffixum::makeNumPers(1, 1, 3))->appendSuffix(PossessiveSuffixum::makePossessor(3))->makeDelativus());
    }

    public function testVow()
    {
        $this->assertEquals('00hhhhllh', Phonology::getVowSeq('árvíztűrő tükörfúrógép'));
        $this->assertEquals('high', Phonology::getVow('ember'));
        $this->assertEquals('opening', Phonology::getVow('ház'));
        $this->assertEquals('low', Phonology::getVow('út'));
    }

    public function testStemTypes()
    {
        $this->assertTrue(GFactory::parseNP('ház')->isOpening());
        $this->assertTrue(GFactory::parseV('zöldül')->isOpening());
        $this->assertTrue(GFactory::parseV('zöldül')->appendSuffix(GFactory::parseSuffixum('Ás'))->isOpening());
        $this->assertTrue(GFactory::parseV('zöldül')->appendSuffix(GFactory::parseSuffixum('Ás'))->appendSuffix(PossessiveSuffixum::makeNumPers(3, 2))->isOpening());

        $this->assertTrue(GFactory::parseNP('bátor')->isVolatile());
        $this->assertTrue(GFactory::parseSuffixum('_Vk')->isVolatile(), 'plural suffixum is volatile');
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
        // @todo ld. 309. o.
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
        $this->assertEquals('ember', (string) $N->makeNominativus());
        $this->assertEquals('emberek', (string) $N->makePlural());
        $this->assertEquals('embert', (string) $N->makeAccusativus());
        $this->assertEquals('embereket', (string) $N->makePlural()->makeAccusativus());
        $this->assertEquals('emberért', (string) $N->makeCausalisFinalis());
        $this->assertEquals('emberekért', (string) $N->makePlural()->makeCausalisFinalis());
        $this->assertEquals('miatta', (string) $N->makeCausalis()->pronominalize());
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

}

//$test = new EmberTest();
//$test->runAll();

?>
