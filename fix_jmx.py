import pathlib

p = pathlib.Path('skripsi-test.jmx')
s = p.read_text(encoding='utf-8')

# 1. Fix invalid GenericController -> SimpleController (19 occurrences)
old_controller = '<GenericController guiclass="LogicControllerGui" testclass="GenericController"'
new_controller = '<SimpleController guiclass="SimpleControllerGui" testclass="SimpleController"'
count_controller = s.count(old_controller)
s = s.replace(old_controller, new_controller)

# 2. Inject User Defined Variables into empty TestPlan variables block
old_vars = '''        <elementProp name="TestPlan.user_defined_variables" elementType="Arguments" guiclass="ArgumentsPanel" testclass="Arguments" testname="User Defined Variables" enabled="true">
          <collectionProp name="Arguments.arguments">

          </collectionProp>
        </elementProp>'''

new_vars = '''        <elementProp name="TestPlan.user_defined_variables" elementType="Arguments" guiclass="ArgumentsPanel" testclass="Arguments" testname="User Defined Variables" enabled="true">
          <collectionProp name="Arguments.arguments">
            <elementProp name="skripsi-apotek" elementType="Argument">
              <stringProp name="Argument.name">skripsi-apotek</stringProp>
              <stringProp name="Argument.value">https://skripsi-apotek-iyaf.vercel.app</stringProp>
              <stringProp name="Argument.metadata">=</stringProp>
            </elementProp>
            <elementProp name="HOST_APOTEK" elementType="Argument">
              <stringProp name="Argument.name">HOST_APOTEK</stringProp>
              <stringProp name="Argument.value">skripsi-apotek-iyaf.vercel.app</stringProp>
              <stringProp name="Argument.metadata">=</stringProp>
            </elementProp>
          </collectionProp>
        </elementProp>'''

count_vars = s.count(old_vars)
s = s.replace(old_vars, new_vars)

# 3. Fix domain field: JMeter domain must NOT contain scheme (https://)
old_domain = '<stringProp name="HTTPSampler.domain">${skripsi-apotek}</stringProp>'
new_domain = '<stringProp name="HTTPSampler.domain">${HOST_APOTEK}</stringProp>'
count_domain = s.count(old_domain)
s = s.replace(old_domain, new_domain)

p.write_text(s, encoding='utf-8')

print(f"Replaced {count_controller} GenericController -> SimpleController")
print(f"Injected User Defined Variables: {'OK' if count_vars == 1 else 'WARNING, count=' + str(count_vars)}")
print(f"Updated {count_domain} HTTPSampler.domain fields")