export class ContextManager {
  constructor(stack = []) {
    this._stack = Array.isArray(stack) ? stack.map(c => ({ ...c })) : [];
  }

  has(name) {
    return this._stack.some(c => c.name === name && c.lifespan > 0);
  }

  set(name, lifespan = 5) {
    const existing = this._stack.find(c => c.name === name);
    if (existing) {
      existing.lifespan = lifespan;
    } else {
      this._stack.push({ name, lifespan });
    }
  }

  clear(name) {
    this._stack = this._stack.filter(c => c.name !== name);
  }

  tick() {
    this._stack = this._stack
      .map(c => ({ ...c, lifespan: c.lifespan - 1 }))
      .filter(c => c.lifespan > 0);
  }

  active() {
    return this._stack.filter(c => c.lifespan > 0).map(c => c.name);
  }

  toArray() {
    return this._stack.filter(c => c.lifespan > 0).map(c => ({ ...c }));
  }
}
