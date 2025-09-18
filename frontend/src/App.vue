<template>
  <div class="container">
    <div class="header">
      <h1>📚 Biblioteca de Libros</h1>
      <p>Explora nuestra colección de libros con reseñas</p>
    </div>

    <button 
      @click="loadBooks" 
      :disabled="loading" 
      class="refresh-btn"
    >
      {{ loading ? 'Cargando...' : 'Actualizar Lista' }}
    </button>

    <div v-if="error" class="error">
      {{ error }}
    </div>

    <div v-if="loading && !books.length" class="loading">
      <p>Cargando libros...</p>
    </div>

    <div v-else-if="books.length === 0" class="empty-state">
      <h3>No hay libros disponibles</h3>
      <p>Parece que no hay libros en la base de datos aún.</p>
    </div>

    <div v-else class="books-grid">
      <div 
        v-for="book in books" 
        :key="book.title + book.author" 
        class="book-card"
      >
        <h3 class="book-title">{{ book.title }}</h3>
        <p class="book-author">por {{ book.author }}</p>
        <p class="book-year">Año: {{ book.published_year }}</p>
        
        <div class="rating-section">
          <span class="rating-label">Rating promedio:</span>
          <span 
            v-if="book.average_rating" 
            class="rating-value"
          >
            ⭐ {{ book.average_rating }}/5
          </span>
          <span v-else class="rating-none">
            Sin reseñas
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'App',
  data() {
    return {
      books: [],
      loading: false,
      error: null
    }
  },
  async mounted() {
    await this.loadBooks()
  },
  methods: {
    async loadBooks() {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get('http://localhost:8000/api/books')
        this.books = response.data
        console.log('Libros cargados:', this.books)
      } catch (error) {
        console.error('Error al cargar libros:', error)
        this.error = 'Error al cargar los libros. Por favor verifica que el servidor backend esté funcionando.'
        
        if (error.response) {
          this.error += ` (Status: ${error.response.status})`
        } else if (error.request) {
          this.error += ' (No se pudo conectar al servidor)'
        }
      } finally {
        this.loading = false
      }
    }
  }
}
</script>