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
        $N = parseNP('ember');
        $this->assertEquals($N->vow, $N->makeNominativus()->vow);

    }

    public function testPhonology()
    {
        $N = parseNP('ember');
        $N->addSuffix('kA');
        $this->assertEquals('emberke', (string) $N);

        $this->assertEquals('00hhhhllh', Phonology::getVowSeq('árvíztűrő tükörfúrógép'));

        $this->assertEquals('high', Phonology::getVow('ember'));
        $this->assertEquals('opening', Phonology::getVow('ház'));
        $this->assertEquals('low', Phonology::getVow('út'));

        $this->assertEquals(true , Phonology::getPropagatedI('ember'), 'ember');
        $this->assertEquals(false, Phonology::getPropagatedI('ház'), 'ház');
        $this->assertEquals(true , Phonology::getPropagatedI('föld'), 'föld');
        $this->assertEquals(true , Phonology::getPropagatedI('kert'), 'kert');
        $this->assertEquals(true , Phonology::getPropagatedI('kéz'), 'kéz');

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

        $this->assertEquals(false, Phonology::getPropagatedI('út'), 'út');
        $this->assertEquals(true , Phonology::getPropagatedI('kövér'), 'kövér');
        $this->assertEquals(true , Phonology::getPropagatedI('sofőr'), 'sofőr');
        $this->assertEquals(false, Phonology::getPropagatedI('kőfal'), 'kőfal');
        $this->assertEquals(false, Phonology::getPropagatedI('bika'), 'bika');
        $this->assertEquals(false, Phonology::getPropagatedI('nüansz'), 'nüansz');
    }

    public function testNominalCases()
    {
        unset($N);
        $N = parseNP('ember');
        $this->assertEquals('ember', (string) $N->makeNominativus());
        $this->assertEquals('emberek', (string) $N->makePlural());
        $this->assertEquals('embert', (string) $N->makeAccusativus());
        $this->assertEquals('embereket', (string) $N->makePlural()->makeAccusativus());
        $this->assertEquals('embereket', (string) $N->makeAccusativus()->makePlural());
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
        $N = parseNP('út');
        $this->assertEquals('út', (string) $N->makeNominativus());
        //$this->assertEquals('utak', (string) $N->makePlural());
        //$this->assertEquals('utat', (string) $N->makeAccusativus());
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
        $N = parseNP('föld');
        $this->assertEquals('föld', (string) $N->makeNominativus());
        $this->assertEquals('földek', (string) $N->makePlural());
        //$this->assertEquals('földet', (string) $N->makeAccusativus());
        $this->assertEquals('földért', (string) $N->makeCausalisFinalis());
        $this->assertEquals('földnek', (string) $N->makeDativus());
        $this->assertEquals('földdel', (string) $N->makeInstrumentalis());
        $this->assertEquals('földdé', (string) $N->makeTranslativusFactivus());
        $this->assertEquals('földön', (string) $N->makeSuperessivus());
        $this->assertEquals('földhöz', (string) $N->makeAllativus());

    }

    public function testVirtualNominalCases()
    {
        unset($N);
        $N = parseNP('ember');
        $this->assertEquals('embernek', (string) $N->makeGenitivus());
        $this->assertEquals('ember miatt', (string) $N->makeCausalis());
        $this->assertEquals('emberen keresztül', (string) $N->makePerlativus());
        $this->assertEquals('emberen át', (string) $N->makeProlativus());
        $this->assertEquals('emberen át', (string) $N->makeVialis());
        $this->assertEquals('ember alatt', (string) $N->makeSubessivus());
        $this->assertEquals('ember mentén', (string) $N->makeProsecutivus());

        unset($N);
        $N = parseNP('föld');
        $this->assertEquals('földön keresztül', (string) $N->makePerlativus());

        unset($N);
        $N = parseNP('út');
        $this->assertEquals('úton keresztül', (string) $N->makePerlativus());
    }

}

//$test = new EmberTest();
//$test->runAll();

?>
