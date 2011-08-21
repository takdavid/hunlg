#!/usr/bin/python
# coding=UTF-8

import unittest
from grammar import Phonology, Wordform

class GrammarTest (unittest.TestCase) :

    def testVowelHarmony(self) :
        self.assertEqual(True , Phonology.getPropagatedI('ember'), 'ember')
        self.assertEqual(False, Phonology.getPropagatedI(u'ház'), u'ház')
        self.assertEqual(True , Phonology.getPropagatedI(u'föld'), u'föld')
        self.assertEqual(True , Phonology.getPropagatedI(u'kert'), u'kert')
        self.assertEqual(True , Phonology.getPropagatedI(u'kéz'), u'kéz')
        self.assertEqual(False, Phonology.getPropagatedI(u'út'), u'út')
        self.assertEqual(True , Phonology.getPropagatedI(u'kövér'), u'kövér')
        self.assertEqual(True , Phonology.getPropagatedI(u'sofőr'), u'sofőr')
        self.assertEqual(False, Phonology.getPropagatedI(u'kőfal'), u'kőfal')
        self.assertEqual(False, Phonology.getPropagatedI(u'bika'), u'bika')
        self.assertEqual(False, Phonology.getPropagatedI(u'nüansz'), u'nüansz')

        self.assertEqual(False, Phonology.getPropagatedU(u'ház'), u'ház')
        self.assertEqual(False, Phonology.getPropagatedU(u'kert'), u'kert')
        self.assertEqual(True , Phonology.getPropagatedU(u'föld'), u'föld')
        self.assertEqual(False, Phonology.getPropagatedU(u'kéz'), u'kéz')

        self.assertEqual(False, Phonology.needSuffixI(u'ház'), u'ház');
        self.assertEqual(True , Phonology.needSuffixI(u'kert'), u'kert');
        self.assertEqual(True , Phonology.needSuffixI(u'föld'), u'föld');
        self.assertEqual(True , Phonology.needSuffixI(u'tök'), u'tök');
        self.assertEqual(True , Phonology.needSuffixI(u'kéz'), u'kéz');

        self.assertEqual(False, Phonology.needSuffixU(u'ház'), u'ház');
        self.assertEqual(False, Phonology.needSuffixU(u'kert'), u'kert');
        self.assertEqual(True , Phonology.needSuffixU(u'föld'), u'föld');
        self.assertEqual(True , Phonology.needSuffixU(u'tök'), u'tök');
        self.assertEqual(False, Phonology.needSuffixU(u'kéz'), u'kéz');

    def testAMNY(self) :
        self.assertEqual(u'fá', Phonology.doAMNY(u'fa'))
        self.assertEqual(u'almá', Phonology.doAMNY(u'alma'))
        self.assertEqual(u'medvé', Phonology.doAMNY(u'medve'))

    def testMR(self) :
        self.assertEqual(u'aktiv', Phonology.doMR(u'aktív'))
        self.assertEqual(u'ur', Phonology.doMR(u'úr'))
        self.assertEqual(u'fuzio', Phonology.doMR(u'fúzió'))

    def testInterpolateVowels(self) :
        self.assertEqual(u'UI,-', Phonology.needSuffixPhonocode(u'fül'))
        self.assertEqual(u'hoz', Phonology.interpolateVowels(Phonology.needSuffixPhonocode(u'fal'), u'hOz'))
        self.assertEqual(u'hez', Phonology.interpolateVowels(Phonology.needSuffixPhonocode(u'fék'), u'hOz'))
        self.assertEqual(u'höz', Phonology.interpolateVowels(Phonology.needSuffixPhonocode(u'fül'), u'hOz'))

    def testPhonologyRest(self) :
        self.assertEqual(False, Phonology.isVowel('b'))
        self.assertEqual(True, Phonology.isVowel('a'))
        self.assertEqual(False, Phonology.isAffrikate('b'))
        self.assertEqual(True, Phonology.isAffrikate('cs'))
        self.assertEqual(False, Phonology.isSybyl('b'))
        self.assertEqual(True, Phonology.isSybyl('s'))

    def testDoubleConsonant(self) :
        self.assertEqual(u'bb', Phonology.doubleConsonant(u'b'))
        self.assertEqual(u'bb', Phonology.doubleConsonant(u'bb'))
        self.assertEqual(u'cc', Phonology.doubleConsonant(u'c'))
        self.assertEqual(u'ccs', Phonology.doubleConsonant(u'cs'))
        self.assertEqual(u'ccs', Phonology.doubleConsonant(u'ccs'))
        self.assertEqual(u'dd', Phonology.doubleConsonant(u'd'))
        self.assertEqual(u'ddz', Phonology.doubleConsonant(u'dz'))
        self.assertEqual(u'ddz', Phonology.doubleConsonant(u'ddz'))
        self.assertEqual(u'ddzs', Phonology.doubleConsonant(u'dzs'))
        self.assertEqual(u'ddzs', Phonology.doubleConsonant(u'ddzs'))
        self.assertEqual(u'ff', Phonology.doubleConsonant(u'f'))
        self.assertEqual(u'ff', Phonology.doubleConsonant(u'ff'))
        self.assertEqual(u'gg', Phonology.doubleConsonant(u'g'))
        self.assertEqual(u'gg', Phonology.doubleConsonant(u'gg'))
        self.assertEqual(u'ggy', Phonology.doubleConsonant(u'gy'))
        self.assertEqual(u'ggy', Phonology.doubleConsonant(u'ggy'))
        self.assertEqual(u'hh', Phonology.doubleConsonant(u'h'))
        self.assertEqual(u'jj', Phonology.doubleConsonant(u'j'))
        self.assertEqual(u'jj', Phonology.doubleConsonant(u'jj'))
        self.assertEqual(u'kk', Phonology.doubleConsonant(u'k'))
        self.assertEqual(u'kk', Phonology.doubleConsonant(u'kk'))
        self.assertEqual(u'll', Phonology.doubleConsonant(u'l'))
        self.assertEqual(u'll', Phonology.doubleConsonant(u'll'))
        self.assertEqual(u'lly', Phonology.doubleConsonant(u'ly'))
        self.assertEqual(u'lly', Phonology.doubleConsonant(u'lly'))
        self.assertEqual(u'mm', Phonology.doubleConsonant(u'm'))
        self.assertEqual(u'mm', Phonology.doubleConsonant(u'mm'))
        self.assertEqual(u'nn', Phonology.doubleConsonant(u'n'))
        self.assertEqual(u'nn', Phonology.doubleConsonant(u'nn'))
        self.assertEqual(u'nny', Phonology.doubleConsonant(u'ny'))
        self.assertEqual(u'nny', Phonology.doubleConsonant(u'nny'))
        self.assertEqual(u'pp', Phonology.doubleConsonant(u'p'))
        self.assertEqual(u'pp', Phonology.doubleConsonant(u'pp'))
        self.assertEqual(u'qq', Phonology.doubleConsonant(u'q'))
        self.assertEqual(u'rr', Phonology.doubleConsonant(u'r'))
        self.assertEqual(u'rr', Phonology.doubleConsonant(u'rr'))
        self.assertEqual(u'ss', Phonology.doubleConsonant(u's'))
        self.assertEqual(u'ss', Phonology.doubleConsonant(u'ss'))
        self.assertEqual(u'ssz', Phonology.doubleConsonant(u'sz'))
        self.assertEqual(u'ssz', Phonology.doubleConsonant(u'ssz'))
        self.assertEqual(u'tt', Phonology.doubleConsonant(u't'))
        self.assertEqual(u'tt', Phonology.doubleConsonant(u'tt'))
        self.assertEqual(u'tty', Phonology.doubleConsonant(u'ty'))
        self.assertEqual(u'tty', Phonology.doubleConsonant(u'tty'))
        self.assertEqual(u'vv', Phonology.doubleConsonant(u'v'))
        self.assertEqual(u'vv', Phonology.doubleConsonant(u'vv'))
        self.assertEqual(u'ww', Phonology.doubleConsonant(u'w'))
        self.assertEqual(u'xx', Phonology.doubleConsonant(u'x'))
        self.assertEqual(u'zz', Phonology.doubleConsonant(u'z'))
        self.assertEqual(u'zz', Phonology.doubleConsonant(u'zz'))
        self.assertEqual(u'zzs', Phonology.doubleConsonant(u'zs'))
        self.assertEqual(u'zzs', Phonology.doubleConsonant(u'zzs'))

        self.assertEqual(u'bika', Phonology.doDoubleLastConsonant(u'bika'))
        self.assertEqual(u'házz', Phonology.doDoubleLastConsonant(u'ház'))
        self.assertEqual(True, Phonology.canAssimilate(u'ház', u'val', u'v'))
        self.assertEqual(False, Phonology.canAssimilate(u'bika', u'val', u'v'))

    def testLastConsonant(self) :
        self.assertEqual(u'b', Phonology.getLastConsonant(u'galamb'))
        self.assertEqual(u'bb', Phonology.getLastConsonant(u'szebb'))
        self.assertEqual(u'c', Phonology.getLastConsonant(u'léc'))
        self.assertEqual(u'cs', Phonology.getLastConsonant(u'mécs'))
        self.assertEqual(u'ccs', Phonology.getLastConsonant(u'meccs'))
        self.assertEqual(u'd', Phonology.getLastConsonant(u'fajd'))
        self.assertEqual(u'dz', Phonology.getLastConsonant(u'edz'))
        self.assertEqual(u'ddz', Phonology.getLastConsonant(u'xeddz'))
        self.assertEqual(u'dzs', Phonology.getLastConsonant(u'bridzs'))
        self.assertEqual(u'ddzs', Phonology.getLastConsonant(u'briddzs'))
        self.assertEqual(u'f', Phonology.getLastConsonant(u'xef'))
        self.assertEqual(u'ff', Phonology.getLastConsonant(u'xeff'))
        self.assertEqual(u'g', Phonology.getLastConsonant(u'ág'))
        self.assertEqual(u'gg', Phonology.getLastConsonant(u'agg'))
        self.assertEqual(u'gy', Phonology.getLastConsonant(u'megy'))
        self.assertEqual(u'ggy', Phonology.getLastConsonant(u'meggy'))
        self.assertEqual(u'h', Phonology.getLastConsonant(u'düh'))
        self.assertEqual(u'j', Phonology.getLastConsonant(u'díj'))
        self.assertEqual(u'jj', Phonology.getLastConsonant(u'fejj'))
        self.assertEqual(u'k', Phonology.getLastConsonant(u'mák'))
        self.assertEqual(u'kk', Phonology.getLastConsonant(u'makk'))
        self.assertEqual(u'l', Phonology.getLastConsonant(u'ál'))
        self.assertEqual(u'll', Phonology.getLastConsonant(u'áll'))
        self.assertEqual(u'ly', Phonology.getLastConsonant(u'mély'))
        self.assertEqual(u'lly', Phonology.getLastConsonant(u'gally'))
        self.assertEqual(u'm', Phonology.getLastConsonant(u'ham'))
        self.assertEqual(u'mm', Phonology.getLastConsonant(u'hamm'))
        self.assertEqual(u'n', Phonology.getLastConsonant(u'mén'))
        self.assertEqual(u'nn', Phonology.getLastConsonant(u'benn'))
        self.assertEqual(u'ny', Phonology.getLastConsonant(u'lány'))
        self.assertEqual(u'nny', Phonology.getLastConsonant(u'szenny'))
        self.assertEqual(u'p', Phonology.getLastConsonant(u'szép'))
        self.assertEqual(u'pp', Phonology.getLastConsonant(u'csepp'))
        self.assertEqual(u'q', Phonology.getLastConsonant(u'faq'))
        self.assertEqual(u'r', Phonology.getLastConsonant(u'dir'))
        self.assertEqual(u'rr', Phonology.getLastConsonant(u'durr'))
        self.assertEqual(u's', Phonology.getLastConsonant(u'dés'))
        self.assertEqual(u'ss', Phonology.getLastConsonant(u'ess'))
        self.assertEqual(u'sz', Phonology.getLastConsonant(u'ész'))
        self.assertEqual(u'ssz', Phonology.getLastConsonant(u'xessz'))
        self.assertEqual(u't', Phonology.getLastConsonant(u'lát'))
        self.assertEqual(u'tt', Phonology.getLastConsonant(u'lőtt'))
        self.assertEqual(u'ty', Phonology.getLastConsonant(u'báty'))
        self.assertEqual(u'tty', Phonology.getLastConsonant(u'xetty'))
        self.assertEqual(u'v', Phonology.getLastConsonant(u'hamv'))
        self.assertEqual(u'vv', Phonology.getLastConsonant(u'xevv'))
        self.assertEqual(u'w', Phonology.getLastConsonant(u'how'))
        self.assertEqual(u'x', Phonology.getLastConsonant(u'bix'))
        self.assertEqual(u'z', Phonology.getLastConsonant(u'bűz'))
        self.assertEqual(u'zz', Phonology.getLastConsonant(u'bízz'))
        self.assertEqual(u'zs', Phonology.getLastConsonant(u'bézs'))
        self.assertEqual(u'zzs', Phonology.getLastConsonant(u'xezzs'))

    def testWordform(self) :
        w = Wordform(u'ember')
        self.assertEqual(u'ember', str(w))
        x = w.cloneAs(Wordform)
        self.assertEqual(u'ember', str(x))

    def testPhonology1(self) :
        w = Wordform(u'ember')
        s = Wordform(u'ke')
        self.assertEqual(u'emberke', str(w.appendSuffix(s)))

unittest.main()
