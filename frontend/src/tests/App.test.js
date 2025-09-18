import { describe, it, expect, vi, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'
import App from '../App.vue'
import axios from 'axios'

vi.mock('axios')

describe('App.vue', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('renders properly', () => {
    const wrapper = mount(App)
    expect(wrapper.text()).toContain('Biblioteca de Libros')
    expect(wrapper.text()).toContain('Explora nuestra colección de libros con reseñas')
  })

  it('shows loading state initially', () => {
    const wrapper = mount(App)
    expect(wrapper.find('.refresh-btn').text()).toContain('Actualizar Lista')
  })

  it('loads books on mount', async () => {
    const mockBooks = [
      {
        id: 1,
        title: 'Test Book',
        author: 'Test Author',
        published_year: 2023,
        average_rating: 4.5
      }
    ]

    axios.get.mockResolvedValue({ data: mockBooks })

    const wrapper = mount(App)
    
    // Esperar a que se complete el mounted
    await new Promise(resolve => setTimeout(resolve, 0))
    await wrapper.vm.$nextTick()

    expect(wrapper.vm.books).toEqual(mockBooks)
    expect(wrapper.text()).toContain('Test Book')
    expect(wrapper.text()).toContain('Test Author')
  })

  it('displays error when API call fails', async () => {
    axios.get.mockRejectedValue(new Error('Network Error'))

    const wrapper = mount(App)
    
    // Esperar a que se complete el mounted
    await new Promise(resolve => setTimeout(resolve, 0))
    await wrapper.vm.$nextTick()

    expect(wrapper.vm.error).toContain('Error al cargar los libros')
    expect(wrapper.find('.error').exists()).toBe(true)
  })

  it('shows empty state when no books', async () => {
    axios.get.mockResolvedValue({ data: [] })

    const wrapper = mount(App)
    
    // Esperar a que se complete el mounted
    await new Promise(resolve => setTimeout(resolve, 0))
    await wrapper.vm.$nextTick()

    expect(wrapper.find('.empty-state').exists()).toBe(true)
    expect(wrapper.text()).toContain('No hay libros disponibles')
  })

  it('displays book rating correctly', async () => {
    const mockBooks = [
      {
        id: 1,
        title: 'Book with Rating',
        author: 'Author',
        published_year: 2023,
        average_rating: 4.2
      },
      {
        id: 2,
        title: 'Book without Rating',
        author: 'Author 2',
        published_year: 2022,
        average_rating: null
      }
    ]

    axios.get.mockResolvedValue({ data: mockBooks })

    const wrapper = mount(App)
    
    await new Promise(resolve => setTimeout(resolve, 0))
    await wrapper.vm.$nextTick()

    expect(wrapper.text()).toContain('⭐ 4.2/5')
    expect(wrapper.text()).toContain('Sin reseñas')
  })

  it('refresh button works correctly', async () => {
    const mockBooks = [
      {
        id: 1,
        title: 'Refreshed Book',
        author: 'Author',
        published_year: 2023,
        average_rating: 3.8
      }
    ]

    axios.get.mockResolvedValue({ data: mockBooks })

    const wrapper = mount(App)
    
    // Esperar mounted inicial
    await new Promise(resolve => setTimeout(resolve, 0))
    await wrapper.vm.$nextTick()

    // Limpiar mock y hacer nueva llamada
    vi.clearAllMocks()
    axios.get.mockResolvedValue({ data: mockBooks })

    // Hacer click en refresh
    await wrapper.find('.refresh-btn').trigger('click')
    await wrapper.vm.$nextTick()

    expect(axios.get).toHaveBeenCalledWith('http://localhost:8000/api/books')
  })
})