class Grouper(object):
   """This class provides a lightweight way to group arbitrary objects
together into disjoint sets when a full-blown graph data structure
would be overkill.

Objects can be joined using .join(), tested for connectedness
using .joined(), and all disjoint sets can be retreived using
.get().

The objects being joined must be hashable.

For example:

>>> g = grouper.Grouper()
>>> g.join('a', 'b')
>>> g.join('b', 'c')
>>> g.join('d', 'e')
>>> list(g.get())
[['a', 'b', 'c'], ['d', 'e']]
>>> g.joined('a', 'b')
True
>>> g.joined('a', 'c')
True
>>> g.joined('a', 'd')
False"""   
   def __init__(self, init=[]):
      mapping = self._mapping = {}
      for x in init:
         mapping[x] = [x]
        
   def join(self, a, *args):
      """Join given arguments into the same set.
Accepts one or more arguments."""
      mapping = self._mapping
      set_a = mapping.setdefault(a, [a])

      for arg in args:
         set_b = mapping.get(arg)
         if set_b is None:
            set_a.append(arg)
            mapping[arg] = set_a
         elif not set_b is set_a:
            set_a.extend(set_b)
            for elem in set_b:
               mapping[elem] = set_a

   def joined(self, a, b):
      """Returns True if a and b are members of the same set."""
      mapping = self._mapping
      set_a = mapping.get(a)
      if not set_a is None:
         set_b = mapping.get(b)
         return set_b is set_a

   def get(self):
      """Returns an iterator returning each of the disjoint sets as a list."""
      keys = self._mapping.copy()
      while len(keys):
         key, val = keys.popitem()
         yield val
         for i in val:
            if not i is key:
               del keys[i]

